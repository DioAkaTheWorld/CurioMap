<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="fermer">
    <div class="modal-content">
      <span class="close-btn" @click="fermer">&times;</span>
      <h2>Ajouter un point</h2>
      <form @submit.prevent="soumettre">
        <div class="form-group">
          <label>Titre</label>
          <input v-model="form.titre" type="text" required class="form-control" />
        </div>

        <div class="form-group">
          <label>Catégorie</label>
          <select v-model="form.categorie" class="form-control">
             <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.label }}
             </option>
          </select>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea v-model="form.description" class="form-control" rows="3"></textarea>
        </div>

        <div @click="optionsVisibles = !optionsVisibles" class="options-header">
          <h2>Champs optionnels</h2>
          <span :class="{'rotated': optionsVisibles}">▼</span>
        </div>

        <div v-show="optionsVisibles" class="options-content">
          <div class="coords-row">
            <div class="form-group">
              <label>Date début</label>
              <input v-model="form.dateDebut" type="date" class="form-control" />
            </div>
            <div class="form-group">
              <label>Date fin</label>
              <input v-model="form.dateFin" type="date" class="form-control" />
            </div>
          </div>

          <div class="coords-row">
            <div class="form-group">
              <label>Heure début</label>
              <input v-model="form.heureDebut" type="time" class="form-control" />
            </div>
            <div class="form-group">
              <label>Heure fin</label>
              <input v-model="form.heureFin" type="time" class="form-control" />
            </div>
          </div>
        </div>

        <div class="coords-row">
          <div class="form-group">
            <label>Latitude</label>
            <input :value="latitude" type="text" readonly class="form-control readonly" />
          </div>
          <div class="form-group">
            <label>Longitude</label>
            <input :value="longitude" type="text" readonly class="form-control readonly" />
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Créer</button>
          <button type="button" @click="fermer" class="btn btn-secondary">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ModalePoint',
  props: {
    isOpen: {
      type: Boolean,
      required: true
    },
    latitude: {
      type: Number,
      required: true
    },
    longitude: {
      type: Number,
      required: true
    },
    categories: {
        type: Array,
        required: true
    }
  },
  emits: ['close', 'submit'],
  data() {
    return {
      optionsVisibles: false,
      form: {
        titre: '',
        categorie: 1,
        description: '',
        dateDebut: '',
        dateFin: '',
        heureDebut: '',
        heureFin: ''
      }
    }
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        this.resetForm();
      }
    }
  },
  methods: {
    resetForm() {
      this.optionsVisibles = false;
      this.form = {
        titre: '',
        categorie: 1,
        description: '',
        dateDebut: '',
        dateFin: '',
        heureDebut: '',
        heureFin: ''
      };
    },

    fermer() {
      this.$emit('close');
    },

    soumettre() {
      //Préparation et validation des dates
      let dateDebutISO = null;
      if (this.form.dateDebut) {
        const time = this.form.heureDebut ? `${this.form.heureDebut}:00` : '00:00:00';
        dateDebutISO = `${this.form.dateDebut} ${time}`;
      }

      let dateFinISO = null;
      if (this.form.dateFin) {
        const time = this.form.heureFin ? `${this.form.heureFin}:00` : '00:00:00';
        dateFinISO = `${this.form.dateFin} ${time}`;
      }

      if (dateDebutISO && dateFinISO) {
        const start = new Date(dateDebutISO);
        const end = new Date(dateFinISO);
        if (end < start) {
            alert('La date de fin doit être avant ou égale à la date de début.');
            return;
        }
      }

      const payload = {
        ...this.form,
        categorie: parseInt(this.form.categorie),
        latitude: this.latitude,
        longitude: this.longitude,
        dateDebut: dateDebutISO,
        dateFin: dateFinISO
      };

      this.$emit('submit', payload);
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 20000;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 24px;
  cursor: pointer;
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
.form-control {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
  border: 1px solid #ddd;
  border-radius: 4px;
}
.form-control.readonly {
  background: #eee;
}
.coords-row {
  display: flex;
  gap: 15px;
}
.coords-row .form-group {
  flex: 1;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.btn {
  padding: 8px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.btn-primary {
  background: #3388ff;
  color: white;
}
.btn-secondary {
  background: #ccc;
  color: black;
}
.options-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background-color: #f9f9f9;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 10px;
    border: 1px solid #eee;
}
.options-header h2 {
    margin: 0;
    font-size: 1.1rem;
    color: #555;
}
.options-header span {
    transition: transform 0.3s ease;
    font-size: 0.8rem;
    color: #777;
}
.options-header span.rotated {
    transform: rotate(180deg);
}
.options-content {
    background-color: #fafafa;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #eee;
}
</style>

