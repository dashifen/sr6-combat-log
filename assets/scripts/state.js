import {createStore} from 'vuex';

export const state = createStore({
  state() {
    return {
      characters: {}
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
     * Uses the data parameter to make a change to a character.
     *
     * @param state
     * @param {{index: number, property: string, value: * }} data
     */
    setCharacterProperty(state, data) {
      const character = state.characters[data.index];
      const forceRoll = data.property === 'roll' && data.value === -1;
      if (!forceRoll) {
        
        // if we're not here to force the roll for a player, we set this
        // character's property to the value passed here.  notice that we want
        // to keep things numeric if it's not the notes field.
        
        character[data.property] = data.property !== 'notes'
          ? Number(data.value)
          : data.value;
      }
      
      // now, if we are here to force a roll or if we just messed with the
      // attributes that make up a character's roll, we call the roll method
      // below to calculate it.  then, we calculate their initiative score
      // based on their roll and other impacting factors.
      
      if (forceRoll || data.property === 'initiative' || data.property === 'dice') {
        character.roll = roll(character);
      }
      
      character.score = calculateScore(character);
    },
    
    /**
     * Records a major or minor action for a character.
     *
     * @param state
     * @param {{index: number, type: string, value: number }} data
     */
    recordAction(state, data) {
      const character = state.characters[data.index];
      character[data.type] += data.value;
    },
    
    /**
     * Adds an NPC to our list of characters.
     *
     * @param state
     * @param {string} name
     */
    addCharacter(state, name) {
      fetch('/session/new-character?name=' + name)
        .then(response => response.json())
        
        // when the server responds with our new character, we can just push
        // it into our state.  this does put them at the bottom of the list,
        // but that's okay; we can just click the sort button to move them when
        // we're ready to get them into the initiative order.
        
        .then(character => state.characters.push(character));
    },
    
    /**
     * Removes a character from our on-screen list.
     *
     * @param state
     * @param {string} name
     */
    removeCharacter(state, name) {
      state.characters = state.characters.filter(character => character.name !== name);
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
        state.characters[i].major = 0;
        state.characters[i].minor = 0;
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
  let roll = Number(character.initiative);
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
  
  if (character.lowPainTolerance) {
    modification *= 2;
  }
  
  if (character.highPainTolerance) {
    modification -= 1;
  }
  
  return character.roll - modification;
}

/**
 * Returns a metatype for an un-named grunt.
 *
 * @returns {string}
 */
function getMetatype() {
  const type = d(100);
  
  if (type <= 66) {
    return 'Human';
  } else if (type <= 79) {
    return 'Elf';
  } else if (type <= 81) {
    return 'Dwarf';
  } else if (type <= 97) {
    return 'Ork';
  } else if (type <= 99) {
    return 'Troll';
  } else {
    return 'Other';
  }
}
