import {createApp} from 'vue';
import Session from './components/session.vue';

const SR6CombatLog = {
  /**
   * Initializes our combat session as needed.
   *
   * @return void
   */
  init: () => {
    document.documentElement.classList.remove('no-js');
    const session = document.querySelector('session');
    if (session) createApp(Session).mount(session);
  }
};

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', SR6CombatLog.init)
  : SR6CombatLog.init();
