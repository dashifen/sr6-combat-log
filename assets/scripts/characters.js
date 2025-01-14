export const characters = {
  players: (async () => {
    const response = await fetch('/characters');
    return await response.json();
  })(),
  
  addCharacter(name) {
    return {
      "name": name,
      "type": "npc",
      "reaction": 1,
      "intuition": 1,
      "initiative": 2,
      "dice": 1,
      "roll": 0,
      "score": 0,
      "actions": {
        "major": false,
        "minor": [
          false,
          false,
        ]
      },
      "damage": 0,
      "lowPainTolerance": false,
      "highPainTolerance": 0,
      "edge": 1,
      "notes": ""
    };
  }
};
