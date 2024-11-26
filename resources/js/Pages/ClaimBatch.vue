<template>
     <div class="container">
      <div class="row">
        <div class="col-md-12">
            <div class="card mt-5" style="padding : 20px">
                <h4>Specialty List</h4><hr>
                <div class="col-lg-4 col-sm-4 mb-2">
                    <a href="#" @click="submitSpecialty()" class="btn btn-success btn-lg mb-5 sm-top">Process Claim Batch</a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Batch Name</th>
                            <th>Insurer</th>
                            <th>Batch Date</th>
                            <th>Status</th>
                            <th>Total Claim</th>
                            <th>Specialty Type</th>
                            <th>Monetary Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(batch, index) in batches" :key="index">
                            <td>{{index + 1}}</td>
                            <td>{{batch.batch_name}}</td>
                            <td>{{batch.insurer.name}}</td>
                            <td>{{batch.batch_date}}</td>
                            <td>{{batch.status}}</td>
                            <td>{{batch.total_claims}}</td>
                            <td>{{batch.monetary_value}}</td>
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
        batch_name: '',
        batch_date: '',
        status: '',
        total_claims: '',
        monetary_value: '',
        batches: [],
        formErrors: {}
      };
    },
    methods: {
      async submitSpecialty() {
        try {
            axios.get('/api/process_batches').then((response) => {
            this.batches = response.data;
            });
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
      axios.get('/api/get_all_batched').then((response) => {
          this.batches = response.data;
        });
    },
  };
</script>