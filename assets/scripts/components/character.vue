<script>
  export default {
    props: ['name', 'dice', 'edge']
  }
</script>

<template>
  <tr>
    <th scope="row" :id="name" headers="character">{{ name }}</th>
    <td :headers="name + ' initiative'"></td>
    <td :headers="name + ' initiative-dice'"></td>
    <td :headers="name + ' initiative-roll'"><input type="text"></td>
    <td :headers="name + ' initiative-score'"></td>
    <td :headers="name + ' major-action'"><input type="checkbox"></td>
    <td :headers="name + ' minor-actions'"><input type="checkbox"></td>

    <td v-for="i in 5" :headers="name + ' minor-actions'">
      <input v-if="i <= dice" type="checkbox">
    </td>

    <td :headers="name + ' edge'"><input type="number" min="0" max="7" :value="edge"></td>
    <td :headers="name + ' damage'"><input type="number" min="0" max="20"></td>
    <td :headers="name + ' notes'"><input type="text"></td>
  </tr>
</template>
