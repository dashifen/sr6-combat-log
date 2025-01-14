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
    
    // the template variable is defined in the default.twig layout such that
    // it's the name of the twig file that extends that layout except for the
    // .twig extension.  then, we've carefully created methods below that match
    // the possible values that variable, and we can call those methods as
    // follows:
    
    this[template]();
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
    
    const group = document.querySelector('optgroup');
    const makeOption = (name) => new Option(name, name.toLowerCase());
    
    fetch('/characters?namesOnly=true')
      .then((response) => response.json())
      .then((characters) => characters.forEach(
        character => group.appendChild(makeOption(character)))
      );
  },
  
  /**
   * Instantiates our Vue app providing it a Vuex store object imported above
   * as state.
   *
   * @return {void}
   */
  session() {
    fetch('/characters')
      .then((response) => response.json())
      .then((characters) => {
        
        // now that we've gotten our list of characters, we commit them into
        // our state object.  that initializes our state with the information
        // in our database.  then, we can inform our Vue app to use that state
        // and mount it to the DOM.
        
        state.commit('setCharacters', characters);
        createApp(Session).use(state).mount(document.querySelector('session'));
      });
  }
};

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', SR6CombatLog.init.bind(SR6CombatLog))
  : SR6CombatLog.init.bind(SR6CombatLog);
