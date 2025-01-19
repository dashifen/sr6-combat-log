import {createStore} from 'vuex';

export const state = createStore({
  state() {
    
    // the blank character contained herein is never actually used.  instead,
    // the setCharacters mutation is used in the application.js initialization
    // to replace it with the actual characters in a combat session.  but, by
    // listing this object here, it allows an IDE to know what the properties
    // of our character objects are.
    
    return {
      characters: [{
        character_id: 0,
        name: '',
        type: '',
        reaction: 1,
        intuition: 1,
        dice: 1,
        edge: 0,
        low_pain_tolerance: 0,
        high_pain_tolerance: 0,
        roll: 0,
        score: 0,
        damage: 0,
        notes: '',
        actions: {
          major: false,
          minor: [false, false, false, false, false, false],
        },
      }]
    };
  },
  
  getters: {
    /**
     * Returns a specific character.
     *
     * @param state
     * @returns object
     */
    character: (state) => (index) => {
      return state.characters[index];
    }
  },
  
  mutations: {
    /**
     * Sets the characters property with a list of character data gathered
     * outside this object, e.g. when the application is loaded.
     *
     * @param state
     * @param characters
     */
    setCharacters(state, characters) {
      this.state.characters = characters;
    },
    
    /**
     * Adds a character to this combat session.
     *
     * @param state
     * @param {number|string} character
     */
    addCharacter(state, character) {
      fetch('/session/character/add?character=' + character)
        .then(response => response.json())
        
        // the server responds with a new character object.  the way we get
        // that onto the screen is by pushing it onto our state's list of
        // characters.  Vue will take over from there.
        
        .then(character => state.characters.push(character));
    },
    
    /**
     * Removes a character from our on-screen list.
     *
     * @param state
     * @param {{character_id: number, from: string}} data
     */
    removeCharacter(state, data) {
      fetch('/session/character/remove', {
        body: objectToFormData(data),
        method: 'POST',
      })
        .then(response => response.json())
        .then(response => {
          
          // when we get back from the server, if it wasn't successful, we'll
          // simply flag an alert on-screen.  otherwise, we filter the list of
          // characters and keep those that don't match the character id in
          // our data object.
          
          if (!response.success) {
            return alert("Couldn't delete.");
          }
          
          const filter = char => char.character_id !== data.character_id;
          state.characters = state.characters.filter(filter);
        });
    },
    
    /**
     * Sends character data to the server where it updates the database.
     *
     * @param state
     * @param {number} index
     */
    updateCharacter(state, index) {
      const character = state.characters[index];
      
      fetch('/session/character/update', {
        method: 'POST',
        body: objectToFormData({
          character_id: character.character_id,
          reaction: character.reaction,
          intuition: character.intuition,
          dice: character.dice,
          edge: character.edge,
          roll: character.roll,
          score: character.score,
          actions: convertActions(character),
          damage: character.damage,
          notes: character.notes,
        })
      })
        .then(response => response.json())
        .then(response => !response.success
          ? console.log("Couldn't update.")
          : '');
    },
    
    /**
     * Rolls for a character's initiative.
     *
     * @param state
     * @param index
     */
    roll(state, index) {
      state.characters[index].roll = roll(state.characters[index]);
    },
    
    /**
     * Calculates a character's initiative score.
     *
     * @param state
     * @param {number} index
     */
    score(state, index) {
      state.characters[index].score = calculateScore(state.characters[index]);
    },
    
    /**
     * Sorts the characters in this combat session by initiative score and
     * then in ERIC (edge, reaction, intuition, coin-flip) order.
     *
     * @param state
     */
    sort(state) {
      state.characters.sort((a, b) => {
        
        // two characters are sorted first by initiative score and then by
        // edge, reaction, intuition, and then coin-flip.  we move through
        // these tests if values match.  otherwise, we return -1 or 1 based on
        // if we want to move a character sooner or later in the combat round.
        
        const tests = ['score', 'edge', 'reaction', 'intuition'];
        for (let i = 0; i < tests.length; i++) {
          
          // higher values go sooner in combat rounds.  therefore, if a is
          // greater than b, we counter-intuitively return -1 so that it will
          // move up in the initiative order.
          
          if (a[tests[i]] !== b[tests[i]]) {
            return a[tests[i]] > b[tests[i]] ? -1 : 1;
          }
        }
        
        // finally, if we made it here, we have to test a coin-flip.  we'll
        // roll 1d2 and assume a 1 is a win and 2 is a loss.
        
        return d(2) === 1 ? -1 : 1;
      });
    },
    
    /**
     * Resets each character's actions to start the next round.
     *
     * @param state
     */
    endRound(state) {
      for (let i = 0; i < state.characters.length; i++) {
        state.characters[i].major = false;
        state.characters[i].minor = Array(6).fill(false);
      }
    }
  }
});

/**
 * Rolls 1d6 for each of a character's initiative dice, finds the sum of those
 * rolls, adds that sum to the character's initiative, and returns the result
 * to the calling scope.
 *
 * @param character
 * @returns {number}
 */
function roll(character) {
  let roll = Number(character.reaction) + Number(character.intuition);
  for (let i = 0; i < character.dice; i++) {
    roll += d(6);
  }
  
  return roll;
}

/**
 * Returns a random number between 1 and sides.
 *
 * @param {number} sides
 * @returns {number}
 */
function d(sides) {
  return Math.floor(Math.random() * sides) + 1;
}

/**
 * Calculates a character's initiative score taking into account their roll
 * and damage.
 *
 * @param character
 * @returns {number}
 */
function calculateScore(character) {
  
  // damage impacts a character's initiative score:  every three boxes of
  // damage reduces their initiative score by one.  two qualities change this
  // modification:  low pain tolerance doubles it and high pain tolerance
  // reduces it by one.
  
  let modification = Math.floor(character.damage / 3);
  
  if (character.low_pain_tolerance) {
    modification *= 2;
  }
  
  if (character.high_pain_tolerance) {
    modification -= 1;
  }
  
  return character.roll - modification;
}

/**
 * Converts a random object to a FormData one.
 *
 * @param object
 * @returns {FormData}
 */
function objectToFormData(object) {
  const formData = new FormData();
  Object.keys(object).forEach(key => formData.append(key, object[key]));
  return formData;
}

/**
 * Converts our boolean actions into a string for the database.
 *
 * @param character
 *
 * @return {string}
 */
function convertActions(character) {
  let actions = character.actions.major ? '1' : '0';
  for (let i = 0; i < character.actions.minor.length; i++) {
    actions += character.actions.minor[i] ? '1' : '0';
  }
  
  return actions;
}
