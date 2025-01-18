import {createApp} from 'vue';
import {state} from './state.js';
import {Dialogs} from './dialogs.js';
import Session from './components/session.vue';

export const SR6CombatLog = {
  /**
   * Initializes our combat session as needed.
   *
   * @return {void}
   */
  init() {
    document.documentElement.classList.remove('no-js');
    this.maybeInitializeSession();
    this.maybeInitializeDialogs();
  },
  
  /**
   * Creates and mounts our Vue app if the <session> component is in the DOM.
   *
   * @return {void}
   */
  maybeInitializeSession() {
    const session = document.querySelector('session');
    if (!!session) {
      
      // the characters variable has been added to the <head> of the DOM in the
      // session.twig file.  here, though, we commit them into the state object
      // we imported above and that's how our Vue app leans about and manages
      // them.
      
      state.commit('setCharacters', characters);
      createApp(Session).use(state).mount(session);
    }
  },
  
  /**
   * Initializes our dialogs if there are any in the DOM.
   *
   * @return {void}
   */
  maybeInitializeDialogs() {
    document.querySelectorAll('dialog').forEach(dialog => {
      
      // to keep the code in this file as focused as possible, we've moved the
      // dialog code over to the adjacent dialogs.js file.  so, here we send a
      // reference for each of the <dialog> elements we found in the DOM over
      // to that code and proceed over there.
      
      Dialogs.initializeDialog(dialog);
    });
  }
};

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', SR6CombatLog.init.bind(SR6CombatLog))
  : SR6CombatLog.init.bind(SR6CombatLog);
