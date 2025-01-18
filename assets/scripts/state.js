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
     * Adds an NPC to our list of characters.
     *
     * @param state
     * @param {number} characterId
     */
    addCharacter(state, characterId) {
      console.log(characterId);
      
      /*fetch('/session/character/new?name=' + name)
        .then(response => response.json())
        
        // when the server responds with our new character, we can just push
        // it into our state.  this does put them at the bottom of the list,
        // but that's okay; we can just click the sort button to move them when
        // we're ready to get them into the initiative order.
        
        .then(character => state.characters.push(character));*/
    },
    
    /**
     * Removes a character from our on-screen list.
     *
     * @param state
     * @param {{character_id: number, from: string}} data
     */
    removeCharacter(state, data) {
      state.characters = state.characters.filter(
        character => character.character_id !== data.character_id
      );
      
      // above, we keep characters that aren't the one matching our id.
      // now, we send information to our server that'll remove them from the
      // session or from the database entirely.
      
      fetch('/session/character/delete', {
        body: objectToFormData(data),
        method: 'POST',
      })
        .then(response => response.json())
        .then(response => !response.success
          ? alert("Couldn't delete.")
          : '');
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
