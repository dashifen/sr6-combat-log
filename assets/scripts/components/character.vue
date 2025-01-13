<script>
  export default {
    props: ['index'],

    computed: {
      character() {
        return this.$store.getters.character(this.index);
      },

      isPlayer() {
        return this.character.type === 'pc';
      }
    },

    methods: {
      _commitChange(property, value) {
        this.$store.commit('setCharacterProperty', {
          index: this.index,
          property: property,
          value: value
        });
      },

      roll() {
        this._commitChange('roll', -1);
      },

      setInitiative(event) {
        this._commitChange('initiative', event.target.value);
      },

      setDice(event) {
        this._commitChange('dice', event.target.value);
      },

      setRoll(event) {
        this._commitChange('roll', event.target.value);
      },

      recordAction(event) {
        this.$store.commit('recordAction', {
          index: this.index,
          type: event.target.dataset.type,
          value: event.target.checked ? 1 : -1,
        });
      },

      setEdge(event) {
        this._commitChange('edge', event.target.value);
      },

      setDamage(event) {
        this._commitChange('damage', event.target.value);
      },

      setNotes(event) {
        this._commitChange('notes', event.target.value);
      }
    }
  };
</script>

<template>
  <tr>
    <th scope="row" :id="character.name" headers="character" class="with-border">
      {{ character.name }}
    </th>
    <td v-if="isPlayer" colspan="2" :headers="character.name + ' initiative'">
      <button @click="roll">Roll</button>
    </td>
    <td v-if="!isPlayer" :headers="character.name + ' initiative'">
      <input type="text" @change="setInitiative" v-model="character.initiative">
    </td>
    <td v-if="!isPlayer" :headers="character.name + ' initiative-dice'">
      <input type="number" min="1" max="5" @change="setDice" v-model="character.dice">
    </td>
    <td :headers="character.name + ' initiative-roll'">
      <input type="text" @change="setRoll" v-model="character.roll">
    </td>
    <td :headers="character.name + ' initiative-score'" class="with-border">
      {{ character.score }}
    </td>

    <td :headers="character.name + ' major-action'" class="with-border">
      <input type="checkbox" data-type="major" v-model="character.actions.major" @click="recordAction">
    </td>

    <td :headers="character.name + ' minor-actions'">
      <input type="checkbox" data-type="minor" data-i="0" v-model="character.actions.minor[0]" @click="recordAction">
    </td>

    <td v-for="i in 5"
      :headers="character.name + ' minor-actions'"
      :class="i === 5 ? 'with-border' : ''"
    >
      <input v-if="i <= character.dice"
        type="checkbox" data-type="minor" :data-i="i"
        v-model="character.actions.minor[i]"
        @click="recordAction"
      >
    </td>

    <td :headers="character.name + ' edge'">
      <input type="number" min="0" max="7" @change="setEdge" v-model="character.edge">
    </td>
    <td :headers="character.name + ' damage'" class="with-border">
      <input type="number" min="0" max="20" value="0" @change="setDamage" v-model="character.damage">
    </td>
    <td :headers="character.name + ' notes'">
      <input type="text" @change="setNotes" v-model="character.notes">
    </td>
  </tr>
</template>
