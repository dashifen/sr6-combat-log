<script setup>
  const props = defineProps(['characters']);
  console.log(props.characters);

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
    </tbody>
    </table>
</template>
