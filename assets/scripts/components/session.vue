<script>
  import character from './character.vue';

  export default {
    components: {
      character
    },

    computed: {
      characters() {
        return this.$store.state.characters;
      }
    },

    methods: {
      addGrunt() {
        this.$store.commit('addCharacter');
      },

      addLieutenant() {
        this.$store.commit('addCharacter', prompt('Enter Name'));
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
          <th scope="col" id="initiative">Init</th>
          <th scope="col" id="initiative-dice">Dice</th>
          <th scope="col" id="initiative-score">Roll</th>
          <th scope="col" id="initiative-score" class="with-border">Score</th>
          <th scope="col" id="major-action" class="with-border">Major<br>Action</th>
          <th scope="col" id="minor-actions" colspan="6" class="with-border">Minor Actions</th>
          <th scope="col" id="edge">Edge</th>
          <th scope="col" id="damage" class="with-border">Damage</th>
          <th scope="col" id="notes">Notes</th>
        </tr>
      </thead>
      <tbody>
        <tr is="vue:character" v-for="index in characters.length" :index="index - 1"></tr>
      </tbody>
    </table>

    <div class="session-controls">
      <button type="button" @click="addGrunt">Add Grunt</button>
      <button type="button" @click="addLieutenant">Add Lieutenant</button>
      <button type="button" @click="sort">Sort</button>
      <button type="button" @click="endRound">End Round</button>
    </div>
  </div>
</template>
