import {createApp} from 'vue';
import {state} from './state.js';
import Session from './components/session.vue';

export const SR6CombatLog = {
  /**
   * Initializes our combat session as needed.
   *
   * @return {void}
   */
  init() {
    document.documentElement.classList.remove('no-js');
    const session = document.querySelector('session');
    if (!!session) {
      
      // the characters variable has been added to the <head> of the DOM in the
      // session.twig file.  here, though, we commit them into the state object
      // we imported above and that's how our Vue app leans about and manages
      // them.
      
      state.commit('setCharacters', characters);
      createApp(Session).use(state).mount(session);
    }
  }
};

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', SR6CombatLog.init.bind(SR6CombatLog))
  : SR6CombatLog.init.bind(SR6CombatLog);
