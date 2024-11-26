<template>
     <div class="container">
      <div class="row">
        <div class="col-md-6">
            <div class="card mt-5" style="padding : 20px">
                <h4>Specialty Form</h4><hr>
                <form @submit.prevent="submitSpecialty">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 mb-4">
                        <label for="speciaty_type">Specialty Type:</label>
                        <input type="text" id="specialty_type" class="form-control" v-model="specialty_type" required />
                        <div v-if="formErrors.specialty_type" class="error">{{ formErrors.specialty_type[0] }}</div>
                        </div>
                        <div class="col-lg-6 col-sm-6 mb-4">
                        <label for="processing_cost">Processing Cost:</label>
                        <input type="text" id="processing_cost" class="form-control" v-model="processing_cost" required />
                        <div v-if="formErrors.processing_cost" class="error">{{ formErrors.processing_cost[0] }}</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg mb-5 sm-top">Submit</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mt-5" style="padding : 20px">
                <h4>Specialty List</h4><hr>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Specialty Type</th>
                            <th>Processing Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(specialty, index) in specialties" :key="index">
                            <td>{{index + 1}}</td>
                            <td>{{specialty.specialty_type}}</td>
                            <td>{{specialty.processing_cost}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</template>
<script>
import axios from 'axios';

  export default {
    data() {
      return {
        specialty_type: '',
          processing_cost: '',
        specialties: [],
        formErrors: {}
      };
    },
    methods: {
      async submitSpecialty() {
        try {
          this.formErrors = {};
          const response = await axios.post('/api/save_specialty', {
            specialty_type: this.specialty_type,
            processing_cost: this.processing_cost,
          });

          if (response.data.error === true)
          {    
            alert('Error encountered in processing your request!');
          } else {
            alert('Specialty submitted successfully!');
            setTimeout(() => {
                location.reload();
            }, 1000);
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            this.formErrors = error.response.data.errors; 
          } else {
            console.error('Unexpected error:', error);
          }
        }
      },
    },
    mounted() {
      // Fetch providers
      axios.get('/api/list_all_specialty').then((response) => {
          this.specialties = response.data.data;
        });
    },
  };
</script>