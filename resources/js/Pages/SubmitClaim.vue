<script>
import axios from 'axios';

  export default {
    data() {
      return {
        insurer_code: '',
        provider_id: '',
        encounter_date: '',
        specialty_id: '',
        priority_level: 1,
        claim_items: [
          { item_name: '', unit_price: 0, quantity: 0, sub_total: 0 }
        ],
        total_amount: 0,
        providers: [],
        specialties: [],
        formErrors: {}
      };
    },
    methods: {
      addItem() {
        this.claim_items.push({ item_name: '', unit_price: 0, quantity: 0, sub_total: 0 });
      },
      removeItem(index) {
        this.claim_items.splice(index, 1);
        this.calculateTotalAmount();
      },
      calculateSubTotal(index) {
        const item = this.claim_items[index];
        item.sub_total = item.unit_price * item.quantity;
        this.calculateTotalAmount();
      },                                      
      calculateTotalAmount() {
        this.total_amount = this.claim_items.reduce((total, item) => total + item.sub_total, 0);
      },
      async submitClaim() {
        try {
          this.formErrors = {};
          const response = await axios.post('/api/process_claim', {
            insurer_code: this.insurer_code,
            provider_id: this.provider_id,
            encounter_date: this.encounter_date,
            specialty_id: this.specialty_id,
            priority_level: this.priority_level,
            claim_items: this.claim_items,
            total_amount: this.total_amount
          });

          if (response.data.error === true)
          {    
            alert('Insurer Code Not correct!');
          } else {
            alert('Claim submitted successfully!');
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
        console.log(response.data.data);
          this.providers = response.data.data;
        });

        // Fetch specialty
        axios.get('/api/list_all_specialty').then((response) => {
          this.specialties = response.data.data;
        });
    },
  };
</script>

<template>
    <div class="container">
      <div class="row">
        <div class="card mt-5" style="padding : 20px">
          <h4>Submit a Claim</h4><hr>
          <form @submit.prevent="submitClaim">
             <div class="row">
                <div class="col-lg-4 col-sm-6 mb-4">
                  <label for="insurer-code">Insurer Code:</label>
                  <input type="text" id="insurer-code" class="form-control" v-model="insurer_code" required />
                <div v-if="formErrors.insurer_code" class="error">{{ formErrors.insurer_code[0] }}</div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                  <label for="provider-name">Provider Name:</label>
                  <!-- <input type="text" id="provider-name" class="form-control" v-model="providerName" required /> -->
                  <select id="provider" v-model="provider_id" class="form-control" required>
                    <option value="" disabled>Select a provider</option>
                    <option v-for="provider in providers" :key="provider.id" :value="provider.id">
                      {{ provider.provider_name }}
                    </option>
                  </select>
                  <div v-if="formErrors.provider_id" class="error">{{ formErrors.provider_id[0] }}</div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                  <label for="encounter-date">Encounter Date:</label>
                  <input type="date" id="encounter-date" class="form-control" v-model="encounter_date" required />
                  <div v-if="formErrors.encounter_date" class="error">{{ formErrors.encounter_date[0] }}</div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                  <label for="specialty">Specialty:</label>
                  <!-- <input type="text" id="specialty" class="form-control" v-model="specialty" required /> -->
                   <select id="provider" v-model="specialty_id" class="form-control" required>
                    <option value="" disabled>Select a specialty</option>
                    <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">
                      {{ specialty.specialty_type }}
                    </option>
                </select>
                <div v-if="formErrors.specialty_id" class="error">{{ formErrors.specialty_id[0] }}</div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                  <label for="priority-level">Priority Level:</label>
                  <input type="number" id="priority-level" class="form-control" v-model="priority_level" min="1" max="5" required />
                  <div v-if="formErrors.priority_level" class="error">{{ formErrors.priority_level[0] }}</div>
                </div>
              </div>
            <div>
              <label>Claim Items:</label>
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Sub Total</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in claim_items" :key="index">
                    <td>
                      <input type="text" v-model="item.item_name" required class="form-control"/>
                      <div v-if="formErrors[`items.${index}.item_name`]" class="error">
                        {{ formErrors[`items.${index}.item_name`][0] }}
                      </div>
                    </td>
                    <td>
                      <input type="number" v-model="item.unit_price" @input="calculateSubTotal(index)" required class="form-control"/>
                       <div v-if="formErrors[`items.${index}.unit_price`]" class="error">
                        {{ formErrors[`items.${index}.unit_price`][0] }}
                      </div>
                    </td>
                    <td>
                      <input type="number" v-model="item.quantity" @input="calculateSubTotal(index)" required class="form-control"/>
                       <div v-if="formErrors[`items.${index}.quantity`]" class="error">
                        {{ formErrors[`items.${index}.amount`][0] }}
                      </div>
                    </td>
                    <td>{{ item.sub_total }}</td>
                    <td>
                      <button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)"><i class="bi bi-trash"></i></button></td>
                  </tr>
                </tbody>
              </table>
              <button type="button" class="btn btn-primary btn-sm sm-top" @click="addItem">+ Add items</button>
            </div><hr>
            <div class="row">
              <div class="col-md-2 offset-10 text-end">
                <span class="me-3 fw-bold">Total Amount:</span>
                <input type="number" id="total-amount" class="form-control" v-model="total_amount" readonly />
              </div>
            </div>
            <button type="submit" class="btn btn-success btn-lg mt-3 mb-5 sm-top">Submit Claim</button>
          </form>
        </div>
      </div>
    </div>
</template>

