export const characters = {
  players: [
    {
      "name": "Cipher",
      "type": "pc",
      "reaction": 6,
      "intuition": 6,
      "initiative": 12,
      "dice": 3,
      "roll": 0,
      "score": 0,
      "actions": {
        "major": false,
        "minor": [
          false,
          false,
          false,
          false,
        ]
      },
      "damage": 0,
      "lowPainTolerance": false,
      "highPainTolerance": 0,
      "edge": 4,
      "notes": ""
    },
    {
      "name": "PawPaw",
      "type": "pc",
      "reaction": 5,
      "intuition": 3,
      "initiative": 8,
      "dice": 2,
      "roll": 0,
      "score": 0,
      "actions": {
        "major": false,
        "minor": [
          false,
          false,
          false,
        ]
      },
      "damage": 0,
      "lowPainTolerance": false,
      "highPainTolerance": 0,
      "edge": 3,
      "notes": ""
    },
    {
      "name": "Sonya",
      "type": "pc",
      "reaction": 6,
      "intuition": 6,
      "initiative": 12,
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
      "edge": 5,
      "notes": ""
    },
    {
      "name": "Tomoya",
      "type": "pc",
      "reaction": 9,
      "intuition": 4,
      "initiative": 13,
      "dice": 4,
      "roll": 0,
      "score": 0,
      "actions": {
        "major": false,
        "minor": [
          false,
          false,
          false,
          false,
          false,
        ]
      },
      "damage": 0,
      "lowPainTolerance": false,
      "highPainTolerance": 0,
      "edge": 3,
      "notes": ""
    },
    {
      "name": "Waver",
      "type": "pc",
      "reaction": 4,
      "intuition": 6,
      "initiative": 10,
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
      "edge": 3,
      "notes": ""
    },
  ],
  
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
