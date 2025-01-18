<script>
  export default {
    props: ['i'],

    computed: {
      isGM() {

        // username is a global variable defined in session.twig.

        return username === 'gm';
      },

      character() {
        return this.$store.getters.character(this.i);
      },

      isPlayer() {
        return this.character.type === 'pc';
      }
    },

    methods: {
      /**
       * Removes a character either from the screen (leaving them in the
       * database) or from the session entirely (deleting them from it).
       *
       * @param {string} name
       */
      remove(name) {
        const dialog = document.getElementById(
          this.isPlayer ? 'pc-remover' : 'npc-remover'
        );

        const interval = setInterval(() => {
          if (!dialog.open) {
            if (['session', 'database'].includes(dialog.returnValue)) {
              this.$store.commit('removeCharacter', {
                character: this.character.character_id,
                from: dialog.returnValue,
              });
            }

            // regardless of what the dialog's return value was, if we closed
            // it, we can clear this interval.  otherwise, it would keep going
            // and waste memory.

            clearInterval(interval);
          }
        }, 500);

        dialog.querySelector('.character-name').innerText = name;
        dialog.showModal();
      },

      /**
       * Handles changes that aren't simple v-model-style updates.
       *
       * @param {FormDataEvent} event
       */
      characterChangeHandler(event) {
        const rollers = ['reaction', 'intuition', 'dice'];
        if (!this.isPlayer && rollers.includes(event.target.name)) {

          // if this isn't a player, and we've changed any of the components
          // that correspond to a character's initiative roll, then we want to
          // produce a roll for this npc.  for players, we assume they tell us
          // their roll, put it in themselves, or we can double-click the roll
          // field.

          this.$store.commit('roll', this.i);
        }

        if (rollers.concat(['roll', 'damage']).includes(event.target.name)) {

          // also, if our change involves any of the above and also this
          // character's roll or the damage, then we'll make sure their
          // initiative score is updated as well.

          this.$store.commit('score', this.i);
        }
      },

      /**
       * Rolls a player's initiative.
       *
       * @param {FormDataEvent} event
       */
      rollPlayer(event) {
        this.$store.commit('roll', this.i);
        event.target.blur();
      }
    }
  };
</script>

<template>
  <tr @change="characterChangeHandler">
    <th scope="row" :id="character.name" headers="character" class="with-border">
      {{ character.name }}
    </th>
    <td :headers="character.name + ' reaction'" class="initiative">
      <input type="number" name="reaction" min="1" max="10" v-model="character.reaction">
    </td>
    <td :headers="character.name + ' intuition'" class="initiative">
      <input type="number" name="intuition" min="1" max="10" v-model="character.intuition">
    </td>
    <td :headers="character.name + ' initiative-dice'" class="initiative">
      <input type="number" name="dice" min="1" max="5" v-model="character.dice">
    </td>
    <td :headers="character.name + ' initiative-roll'" class="initiative">
      <input type="text" name="roll" @dblclick="rollPlayer" v-model="character.roll">
    </td>
    <td :headers="character.name + ' initiative-score'" class="initiative with-border">
      {{ character.score }}
    </td>

    <td :headers="character.name + ' major-action'" class="with-border">
      <input type="checkbox" data-type="major" v-model="character.actions.major">
    </td>

    <td :headers="character.name + ' minor-actions'">
      <input type="checkbox" data-type="minor" data-i="0" v-model="character.actions.minor[0]">
    </td>

    <td v-for="i in 5"
      :headers="character.name + ' minor-actions'"
      :class="i === 5 ? 'with-border' : ''"
    >
      <input :class="i <= character.dice ? 'visible' : 'invisible'"
        type="checkbox" data-type="minor" :data-i="i"
        v-model="character.actions.minor[i]"
      >
    </td>

    <td :headers="character.name + ' edge'">
      <input type="number" name="edge" min="0" max="7" v-model="character.edge">
    </td>
    <td :headers="character.name + ' damage'" class="with-border">
      <input type="number" name="damage" min="0" max="20" value="0" v-model="character.damage">
    </td>
    <td :headers="character.name + ' notes'">
      <input type="text" name="notes" v-model="character.notes">
    </td>
    <td v-if="isGM" :headers="character.name + ' notes'">
      <a href="#" class="closer" @click.prevent="remove(character.name)">&times;</a>
    </td>
  </tr>
</template>
