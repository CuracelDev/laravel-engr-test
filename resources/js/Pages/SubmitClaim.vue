<template>
    <div class="min-h-screen bg-gray-100 py-8">
      <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-center">Submit Claim</h1>
  
        <form @submit.prevent="submitClaim" class="space-y-6">
          <!-- Insurer Code -->
          <div>
            <label for="insurer_code" class="block text-sm font-medium text-gray-700">Insurer Code</label>
            <input
              type="text"
              v-model="form.insurer_code"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
  
          <!-- Provider Name -->
          <div>
            <label for="provider_name" class="block text-sm font-medium text-gray-700">Provider Name</label>
            <input
              type="text"
              v-model="form.provider_name"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
  
          <!-- Encounter Date -->
          <div>
            <label for="encounter_date" class="block text-sm font-medium text-gray-700">Encounter Date</label>
            <input
              type="date"
              v-model="form.encounter_date"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
  
          <!-- Claim Items -->
          <div v-for="(item, index) in form.items" :key="index" class="space-y-4 border p-4 rounded-lg">
            <h3 class="text-lg font-semibold">Item {{ index + 1 }}</h3>
            <div>
              <label :for="`item_name_${index}`" class="block text-sm font-medium text-gray-700">Item Name</label>
              <input
                :id="`item_name_${index}`"
                v-model="item.name"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label :for="`unit_price_${index}`" class="block text-sm font-medium text-gray-700">Unit Price</label>
              <input
                :id="`unit_price_${index}`"
                type="number"
                v-model="item.unit_price"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label :for="`quantity_${index}`" class="block text-sm font-medium text-gray-700">Quantity</label>
              <input
                :id="`quantity_${index}`"
                type="number"
                v-model="item.quantity"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <p class="text-sm text-gray-600">Subtotal: ${{ (item.unit_price * item.quantity).toFixed(2) }}</p>
            </div>
            <button
              type="button"
              @click="removeItem(index)"
              class="w-full bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              Remove Item
            </button>
          </div>
  
          <!-- Add Item Button -->
          <button
            type="button"
            @click="addItem"
            class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Add Item
          </button>
  
          <!-- Total Claim Amount -->
          <div>
            <label for="total_value" class="block text-sm font-medium text-gray-700">Total Claim Amount</label>
            <input
              type="number"
              :value="totalValue"
              readonly
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
  
          <!-- Specialty -->
          <div>
            <label for="specialty" class="block text-sm font-medium text-gray-700">Specialty</label>
            <input
              type="text"
              v-model="form.specialty"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
  
          <!-- Priority Level -->
          <div>
            <label for="priority_level" class="block text-sm font-medium text-gray-700">Priority Level</label>
            <input
              type="number"
              v-model="form.priority_level"
              min="1"
              max="5"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
  
          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            Submit Claim
          </button>
        </form>
  
        <!-- Success/Error Message -->
        <div v-if="message" :class="['mt-4 p-4 rounded-md', messageClass === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
          {{ message }}
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        form: {
          insurer_code: "",
          provider_name: "",
          encounter_date: "",
          items: [{ name: "", unit_price: 0, quantity: 1 }],
          specialty: "",
          priority_level: 1,
        },
        message: "",
        messageClass: "",
      };
    },
    computed: {
      totalValue() {
        return this.form.items.reduce(
          (sum, item) => sum + item.unit_price * item.quantity,
          0
        );
      },
    },
    methods: {
      addItem() {
        this.form.items.push({ name: "", unit_price: 0, quantity: 1 });
      },
      removeItem(index) {
        this.form.items.splice(index, 1);
      },
      async submitClaim() {
        try {
          const response = await axios.post("/api/add-claims", {
            ...this.form,
            total_value: this.totalValue,
          });
  
          this.message = "Claim submitted successfully!";
          this.messageClass = "success";
          this.resetForm();
        } catch (error) {
          this.message = "Error submitting claim. Please try again.";
          this.messageClass = "error";
          console.error(error);
        }
      },
      resetForm() {
        this.form = {
          insurer_code: "",
          provider_name: "",
          encounter_date: "",
          items: [{ name: "", unit_price: 0, quantity: 1 }],
          specialty: "",
          priority_level: 1,
        };
      },
    },
  };
  </script>