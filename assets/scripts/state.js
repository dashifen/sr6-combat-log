import {createStore} from 'vuex';
import {characters} from './characters.js';

export const state = createStore({
  state() {
    return {
      grunts: 0,
      characters: characters,
    };
  },
  
  getters: {
    /**
     * Returns a specific character.
     *
     * @param state
     * @returns {
     *    function(*): {
     *      name: string,
     *      dice: number,
     *      edge: number
     *    }
     *  }
     */
    character: (state) => (index) => {
      return state.characters[index];
    }
  },
  
  mutations: {
  
  }
});
