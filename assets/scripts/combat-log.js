import {createApp} from 'vue';
import Session from './components/session.vue';

const SR6CombatLog = {
  session: null,
  
  /**
   * Initializes our combat session as needed.
   *
   * @return void
   */
  init() {
    document.documentElement.classList.remove('no-js');
    
    if(this.isSession()) {
      createApp(Session).mount(this.session);
    }
  },
  
  /**
   * Returns true if the <session> component is in the DOM.
   *
   * @returns {boolean}
   */
  isSession() {
    
    // notice that we assign this.session, and because the assignment operator
    // returns the value assigned, we can also determine if there's exactly one
    // <session> component in the DOM all in the same line.
    
    return (this.session = document.querySelector('session')).length === 1;
  }
};

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', SR6CombatLog.init)
  : SR6CombatLog.init();
