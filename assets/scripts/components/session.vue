<script setup>
  const props = defineProps(['characters']);
</script>

<template>
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
      <tr v-for="character in characters">
        <th scope="row" :id="character.name" headers="character">
          {{ character.name }}
        </th>

        <td :headers="character.name + ' initiative'">
        </td>

        <td :headers="character.name + ' initiative-dice'">
        </td>

        <td :headers="character.name + ' initiative-roll'">
          <input type="text">
        </td>

        <td :headers="character.name + ' initiative-score'">
        </td>

        <td :headers="character.name + ' major-action'">
          <input type="checkbox">
        </td>

        <td :headers="character.name + ' minor-actions'">
          <input type="checkbox">
        </td>

        <td v-for="i in 5" :headers="character.name + ' minor-actions'">
          <input v-if="i <= character.dice" type="checkbox">
        </td>

        <td :headers="character.name + ' edge'">
          <input type="number" min="0" max="7" :value="character.edge">
        </td>

        <td :headers="character.name + ' damage'">
          <input type="number" min="0" max="20">
        </td>

        <td :headers="character.name + ' notes'">
          <input type="text">
        </td>
      </tr>
    </tbody>
  </table>
</template>
