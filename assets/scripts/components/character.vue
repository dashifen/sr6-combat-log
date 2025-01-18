<script>
  import {Dialogs} from '@/dialogs';

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
       * Removes a character either from the session (leaving them in the
       * database) or from the database entirely.  note:  PCs can't be deleted
       * here.
       *
       * @param {string} characterName
       * @param {number} characterId
       */
      remove(characterName, characterId) {
        const dialogId = this.isPlayer ? 'pc-remover' : 'npc-remover';
        const dialog = document.getElementById(dialogId);

        // the "from" data that we receive from the dialog is either the word
        // session or database indicating how permanently this character is
        // removed.  we pass that as well as the character ID to out $store
        // and let it take over from here.

        Dialogs.watch(dialog, (from) => {
          this.$store.commit('removeCharacter', {
            character_id: characterId,
            from: from
          });
        });

        dialog.querySelector('.character-name').innerText = characterName;
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

        // last, we want to update the server with this change.  to do that
        // we can send information to our store which, in turn, passes it to
        // the server.  all we need to do is tell the store who this was and
        // what just changed.

        this.$store.commit('updateCharacter', {
          character_id: this.character.character_id,
          field: event.target.name,
        });
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
      <input type="checkbox" name="major-action" data-type="major" v-model="character.actions.major">
    </td>

    <td :headers="character.name + ' minor-actions'">
      <input type="checkbox" name="minor-action-0" data-type="minor" data-i="0" v-model="character.actions.minor[0]">
    </td>

    <td v-for="i in 5"
      :headers="character.name + ' minor-actions'"
      :class="i === 5 ? 'with-border' : ''"
    >
      <input :class="i <= character.dice ? 'visible' : 'invisible'"
        type="checkbox" data-type="minor" :name="'minor-action-' + i" :data-i="i"
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
      <a href="#" class="closer"
        @click.prevent="remove(character.name, character.character_id)"
      >&times;</a>
    </td>
  </tr>
</template>
