<template>
  <div class="filter-panel">
    <h3>Filtres</h3>
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
    }
  },
  emits: ['update:modelValue', 'change'],
  methods: {
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
</style>
