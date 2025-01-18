<script>
  import {Dialogs} from '@/dialogs';
  import character from './character.vue';

  export default {
    components: {character},

    computed: {
      characters() {
        return this.$store.state.characters;
      },

      isGM() {

        // the username variable is a global defined in session.twig.

        return username === 'gm';
      }
    },

    methods: {
      addPlayer() {
        const dialog = document.getElementById('pc-adder');
        Dialogs.watch(dialog, (data) => { this.$store.commit('addCharacter', data) });
        dialog.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        dialog.showModal();
      },

      addNonPlayer() {
        const dialog = document.getElementById('npc-adder');
        Dialogs.watch(dialog, (data) => { this.$store.commit('addCharacter', data) });
        dialog.querySelector('input').value = '';
        dialog.showModal();
      },

      sort() {
        this.$store.commit('sort');
      },

      endRound() {
        this.$store.commit('endRound');
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

    <div v-if="isGM" class="session-controls">
      <button type="button" @click="addPlayer">Add PC</button>
      <button type="button" @click="addNonPlayer">Add NPC</button>
      <button type="button" @click="sort">Sort</button>
      <button type="button" @click="endRound">End Round</button>
    </div>
  </div>
</template>
