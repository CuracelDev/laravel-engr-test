<template>
     <div class="container">
      <div class="row">
        <div class="col-md-6">
            <div class="card mt-5" style="padding : 20px">
                <h4>Create New Provider</h4><hr>
                <form @submit.prevent="submitProvider">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 mb-4">
                        <label for="speciaty_type">Provider Name:</label>
                        <input type="text" id="specialty_type" class="form-control" v-model="provider_name" required />
                        <div v-if="formErrors.provider_name" class="error">{{ formErrors.provider_name[0] }}</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg mb-5 sm-top">Submit</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mt-5" style="padding : 20px">
                <h4>Provider list</h4><hr>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Provider Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(provider, index) in providers" :key="index">
                            <td>{{index + 1}}</td>
                            <td>{{provider.provider_name}}</td>
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
        provider_name: '',
        providers: [],
        formErrors: {}
      };
    },
    methods: {
      async submitProvider() {
        try {
          this.formErrors = {};
          const response = await axios.post('/api/save_provider', {
            provider_name: this.provider_name,
          });

          if (response.data.error === true)
          {    
            alert('Error encountered in processing your request!');
          } else {
            alert('Provider created successfully!');
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
      axios.get('/api/list_all_provider').then((response) => {
          this.providers = response.data.data;
        });
    },
  };
</script>