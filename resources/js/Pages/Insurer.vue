<template>
     <div class="container">
      <div class="row">
        <div class="col-md-6">
            <div class="card mt-5" style="padding : 20px">
                <h4>Insurer Form</h4><hr>
                <form @submit.prevent="submitSpecialty">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label for="speciaty_type">Insurer Name:</label>
                            <input type="text" id="name" class="form-control" v-model="name" required />
                            <div v-if="formErrors.name" class="error">{{ formErrors.name[0] }}</div>
                        </div>
                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label for="processing_cost">Capacity Limit:</label>
                            <input type="text" id="processing_cost" class="form-control" v-model="capacity_limit" required />
                            <div v-if="formErrors.capacity_limit" class="error">{{ formErrors.capacity_limit[0] }}</div>
                        </div>
                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label for="processing_cost">Min Batch Size:</label>
                            <input type="text" id="min_batch_size" class="form-control" v-model="min_batch_size" required />
                            <div v-if="formErrors.min_batch_size" class="error">{{ formErrors.min_batch_size[0] }}</div>
                        </div>
                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label for="processing_cost">Max Batch Size:</label>
                            <input type="text" id="max_batch_size" class="form-control" v-model="max_batch_size" required />
                            <div v-if="formErrors.max_batch_size" class="error">{{ formErrors.max_batch_size[0] }}</div>
                        </div>
                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label for="date_preffered_type">Preferred Type:</label>
                            <select id="date_preffered_type" v-model="date_preffered_type" class="form-control" required>
                                <option value="">Select Preferred Type</option>
                                <option value="encounter_date">encounter_date</option>
                                <option value="submission_date">submission_date</option>
                            </select>
                            <div v-if="formErrors.date_preffered_type" class="error">{{ formErrors.date_preffered_type[0] }}</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg mb-5 sm-top">Submit</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mt-5" style="padding : 20px">
                <h4>Insurers List</h4><hr>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Insurer</th>
                            <th>Insurer Code</th>
                            <th>Capacity Limit</th>
                            <th>Min Batch Size</th>
                            <th>Max Batch Size</th>
                            <th>Preference Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(insurer, index) in insurers" :key="index">
                            <td>{{index + 1}}</td>
                            <td>{{insurer.name}}</td>
                            <td>{{insurer.code}}</td>
                            <td>{{insurer.capacity_limit}}</td>
                            <td>{{insurer.min_batch_size}}</td>
                            <td>{{insurer.max_batch_size}}</td>
                            <td>{{insurer.date_preffered_type}}</td>
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
        name: '',
        capacity_limit: '',
        min_batch_size: '',
        max_batch_size: '',
        date_preffered_type: '',
        insurers: [],
        formErrors: {}
      };
    },
    methods: {
      async submitSpecialty() {
        try {
          this.formErrors = {};
          const response = await axios.post('/api/save_insurer', {
            name: this.name,
              capacity_limit: this.capacity_limit,
            min_batch_size: this.min_batch_size,
              max_batch_size: this.max_batch_size,
            date_preffered_type: this.date_preffered_type,
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
      axios.get('/api/list_all_insurer').then((response) => {
          this.insurers = response.data.data;
        });
    },
  };
</script>