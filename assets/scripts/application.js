import {createApp} from 'vue';
import {characters} from './characters.js';
import Session from './components/session.vue';

export const SR6CombatLog = {
  /**
   * Initializes our combat session as needed.
   *
   * @return {void}
   */
  init() {
    document.documentElement.classList.remove('no-js');
    
    // the state variable is defined in the default.twig layout such that it's
    // the name of the template that extends that layout except for the .twig
    // extension.  then, we've carefully created methods below that match the
    // possible values of state, and we can call those methods as follows:
    
    this[state]();
  },
  
  /**
   * Adds the names of characters to an option group in the DOM.
   *
   * @return {void}
   */
  index() {
    
    // in the index state, we want to add our characters to the option group
    // that's ready and waiting for them.  so, for each character, we create
    // an option and then add it within said group.
    
    characters.forEach((character) => {
      const option = new Option(character.name, character.name.toLowerCase());
      document.querySelector('optgroup').appendChild(option);
    });
  },
  
  /**
   * Instantiates our Vue app passing our characters to it as properties.
   *
   * @return {void}
   */
  session() {
    
    // in the session state, we create a Vue app passing it our characters
    // as a property. then, we mount it to the session component which is
    // basically the only thing in the DOM when we're in this state.
    
    createApp(Session, {
      'characters': characters
    }).mount(document.querySelector('session'));
  }
};

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', SR6CombatLog.init.bind(SR6CombatLog))
  : SR6CombatLog.init.bind(SR6CombatLog);
