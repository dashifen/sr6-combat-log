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
        alert('addGrunt');
      },

      addLieutenant() {
        alert('addLieutenant');
      },

      sort() {
        alert('sort');
      },

      endRound() {
        alert('endRound');
      }
    }
  };
</script>

<template>
  <div class="session">
    <table>
      <thead>
        <tr>
          <th scope="col" id="character">Character</th>
          <th scope="col" id="initiative">Init</th>
          <th scope="col" id="initiative-dice">Dice</th>
          <th scope="col" id="initiative-score">Roll</th>
          <th scope="col" id="initiative-score">Score</th>
          <th scope="col" id="major-action">Major<br>Action</th>
          <th scope="col" id="minor-actions" colspan="6">Minor Actions</th>
          <th scope="col" id="edge">Edge</th>
          <th scope="col" id="damage">Damage</th>
          <th scope="col" id="notes">Notes</th>
        </tr>
      </thead>
      <tbody>
        <tr is="vue:character" v-for="c in characters"
          :name="c.name" :dice="c.dice" :edge="c.edge"></tr>
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
