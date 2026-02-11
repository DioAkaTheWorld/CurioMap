<template>
  <div class="filter-panel" :style="{ top: pos.y + 'px', left: pos.x + 'px', transform: 'none' }">
    <h3 @mousedown.prevent="startDrag" :style="{ cursor: dragging ? 'grabbing' : 'grab', userSelect: 'none' }">Filtres</h3>
    <!-- Filtres de catés -->
    <div class="filter-group">
      <h4>Catégories</h4>
      <div v-for="cat in categories" :key="cat.id" class="checkbox-item">
        <input
          type="checkbox"
          :id="'cat-'+cat.id"
          :value="cat.id"
          :checked="modelValue.includes(cat.id)"
          @change="toggleCategory(cat.id)"
        >
        <label :for="'cat-'+cat.id" :style="{color: getCategoryColor(cat.id)}">
          {{ cat.label }}
        </label>
      </div>
    </div>

    <!-- Filtre de distance -->
    <div class="filter-group" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
      <h4>Distance (km)</h4>
      <div class="range-container">
        <input
          type="range"
          min="1"
          max="100"
          step="1"
          :value="distance"
          @input="updateDistance"
          :disabled="!localisationActive"
          style="width: 100%"
        >
        <div style="font-size: 0.8rem; text-align: center; margin-top: 5px; color: #666;">
           <span v-if="!localisationActive">Géolocalisation requise</span>
           <span v-else-if="distance >= 100">Partout</span>
           <span v-else>{{ distance }} km</span>
        </div>
      </div>
    </div>

    <!-- Filtre par date -->
    <div class="filter-group" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
      <h4>Date</h4>
      <div style="display: flex; flex-direction: column; gap: 5px;">
        <label style="font-size: 0.8rem;">Du :</label>
        <input
            type="date"
            :value="dateDebut"
            @input="updateDate('dateDebut', $event.target.value)"
            style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 2px;"
        >

        <label style="font-size: 0.8rem;">Au :</label>
        <input
            type="date"
            :value="dateFin"
            @input="updateDate('dateFin', $event.target.value)"
            style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 2px;"
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Filtre',
  props: {
    categories: {
      type: Array,
      required: true
    },
    modelValue: {
      type: Array,
      required: true
    },
    distance: {
      type: Number,
      default: 100
    },
    localisationActive: {
      type: Boolean,
      default: false
    },
    dateDebut: {
      type: String,
      default: ''
    },
    dateFin: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue', 'update:distance', 'update:dateDebut', 'update:dateFin', 'change'],
  data() {
    return {
      pos: { x: 10, y: 100 },
      dragging: false,
      offset: { x: 0, y: 0 }
    }
  },

  mounted() {
    //Centrer verticalement au chargement approximativement
    this.pos.y = window.innerHeight / 2 - 150;
  },

  methods: {
    startDrag(e) {
      this.dragging = true;
      this.offset.x = e.clientX - this.pos.x;
      this.offset.y = e.clientY - this.pos.y;
      window.addEventListener('mousemove', this.doDrag);
      window.addEventListener('mouseup', this.stopDrag);
    },

    doDrag(e) {
      if (this.dragging) {
        let newX = e.clientX - this.offset.x;
        let newY = e.clientY - this.offset.y;

        const el = this.$el;
        const maxX = window.innerWidth - el.offsetWidth;
        const maxY = window.innerHeight - el.offsetHeight;

        if (newX < 0) newX = 0;
        if (newX > maxX) newX = maxX;
        if (newY < 0) newY = 0;
        if (newY > maxY) newY = maxY;

        this.pos.x = newX;
        this.pos.y = newY;
      }
    },

    stopDrag() {
      this.dragging = false;
      window.removeEventListener('mousemove', this.doDrag);
      window.removeEventListener('mouseup', this.stopDrag);
    },

    updateDistance(event) {
        this.$emit('update:distance', parseInt(event.target.value));
        this.$emit('change');
    },

    updateDate(type, value) {
      this.$emit(`update:${type}`, value);
      this.$emit('change');
    },

    toggleCategory(id) {
      const newSelection = [...this.modelValue];
      const index = newSelection.indexOf(id);

      if (index === -1) {
        newSelection.push(id);
      } else {
        newSelection.splice(index, 1);
      }

      this.$emit('update:modelValue', newSelection);
      this.$emit('change');
    },

    getCategoryColor(id) {
      switch(parseInt(id)) {
        case 1: return '#ff9800'; //Resto en orange
        case 2: return '#774d0e'; //Monument en brun
        case 3: return '#ea5a90'; //Concert en rose
        case 4: return '#4caf50'; //Parc en vert
        case 5: return '#9c27b0'; //Musée en violet
        default: return '#3388ff'; //Par défaut en bleu
      }
    }
  }
}
</script>

<style scoped>
.filter-panel {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    background: white;
    padding: 10px;
    border-radius: 4px;
    border: 2px solid rgba(0,0,0,0.2);
    z-index: 1000;
    width: 150px;
    box-shadow: 0 1px 5px rgba(0,0,0,0.4);
}

.filter-panel h3 {
    margin: 0 0 10px 0;
    font-size: 1rem;
    text-align: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}

.filter-group h4 {
    margin: 0 0 5px 0;
    font-size: 0.9rem;
    color: #555;
}

.checkbox-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.checkbox-item input {
    margin-right: 8px;
    cursor: pointer;
}

.checkbox-item label {
    font-size: 0.85rem;
    cursor: pointer;
    font-weight: bold;
}

.range-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}
</style>
