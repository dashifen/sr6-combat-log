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
    character: (state) => (index) => {
      return state.characters[index];
    }
  }
});
