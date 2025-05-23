<script>
  import {Dialogs} from '@/dialogs';
  import character from './character.vue';

  export default {
    components: {character},

    computed: {
      /**
       * Returns the array of characters in our store in a more convenient
       * way that involves less typing.
       *
       * @returns {any}
       */
      characters() {
        return this.$store.state.characters;
      },

      /**
       * Using the global username variable defined in session.twig, returns
       * true if the current user is the game master.
       *
       * @returns {boolean}
       */
      isGM() {

        // the username variable is a global defined in session.twig.

        return username === 'gm';
      }
    },

    methods: {
      /**
       * Utilizes the dialog#pc-adder modal to collect information from the
       * current user and then adds a new player character to this combat
       * session.
       *
       * @return {void}
       */
      addPlayer() {
        const dialog = document.getElementById('pc-adder');
        Dialogs.watch(dialog, (data) => {
          this.$store.commit('addCharacter', data);
        });
        dialog.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        dialog.showModal();
      },

      /**
       * Utilizes the dialog#npc-adder modal to collect information from the
       * current user and then adds a non-player character to the current
       * combat session.
       *
       * @return {void}
       */
      addNonPlayer() {
        const dialog = document.getElementById('npc-adder');
        Dialogs.watch(dialog, (data) => {
          this.$store.commit('addCharacter', data);
        });
        dialog.querySelector('input').value = '';
        dialog.showModal();
      },

      /**
       * Sorts the on-screen information based on the current initiative
       * scores.
       *
       * @return {void}
       */
      sort() {
        this.$store.commit('sort');
      },

      /**
       * Resets all character actions in preparation for the next round of
       * combat and then also uses the updateCharacter mutation to make sure
       * the server knows about this local change.
       *
       * @return {void}
       */
      endRound() {
        this.$store.commit('endRound');
        for (let i = 0; i < this.characters.length; i++) {
          this.$store.commit('updateCharacter', i);
        }
      },

      /**
       * Confirms and ends a session.
       *
       * @return {void}
       */
      endSession() {
        const dialog = document.getElementById('end-session');

        Dialogs.watch(dialog, (data) => {
          if(data === 'Y') {
            this.$store.commit('endSession');
          }
        });

        dialog.showModal();
      }
    }
  };
</script>

<template>
  <div class="session">
    <table>
      <colgroup>
        <col class="character">
        <col class="initiative" span="4">
        <col class="actions" span="7">
        <col class="stats" span="2">
        <col class="notes">
      </colgroup>
      <thead>
        <tr>
          <th scope="col" id="character" class="with-border">Character</th>
          <th scope="col" id="reaction"><abbr title="Reaction">R</abbr></th>
          <th scope="col" id="intuition"><abbr title="Intuition">I</abbr></th>
          <th scope="col" id="initiative-dice">Dice</th>
          <th scope="col" id="initiative-score">Roll</th>
          <th scope="col" id="initiative-score" class="with-border">Score</th>
          <th scope="col" id="major-action" class="with-border">Major<br>Action</th>
          <th scope="col" id="minor-actions" colspan="6" class="with-border">Minor Actions</th>
          <th scope="col" id="edge">Edge</th>
          <th scope="col" id="damage" class="with-border">Damage</th>
          <th scope="col" id="notes" :colspan="isGM ? 2 : 1">Notes</th>
        </tr>
      </thead>
      <tbody>
        <tr is="vue:character" v-for="i in characters.length" :i="i - 1"></tr>
      </tbody>
    </table>

    <div class="references">
      <a href="/session/references/actions" target="_new"><button type="button">Actions</button></a>
      <a href="/session/references/statuses" target="_new"><button type="button">Statuses</button></a>
      <a href="/session/references/edge" target="_new"><button type="button">Edge</button></a>
    </div>

    <div v-if="isGM" class="session-controls">
      <button type="button" @click="addPlayer">Add PC</button>
      <button type="button" @click="addNonPlayer">Add NPC</button>
      <button type="button" @click="sort">Sort</button>
      <button type="button" @click="endRound">End Round</button>
      <button type="button" @click="endSession">End Session</button>
    </div>
  </div>
</template>
