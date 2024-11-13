<template>
  <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh">
    <div class="order-form container mt-5 p-4 shadow-sm bg-light rounded">
      <h2 class="text-center mb-4">Submit New Order</h2>
      <!-- Flash Messages (Success and Error) -->
      <div v-if="flashMessage" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ flashMessage }}
        <button type="button" class="btn-close" @click="flashMessage = ''" aria-label="Close"></button>
      </div>
      <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ errorMessage }}
        <button type="button" class="btn-close" @click="errorMessage = ''" aria-label="Close"></button>
      </div>

      <!-- Provider Info -->
      <div class="provider-info mb-3">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="hmo_code" class="form-label">HMO Code:</label>
            <input type="text" id="hmo_code" v-model="order.hmo_code" placeholder="Enter HMO Code" class="form-control"
              required />
          </div>
          <div class="col-md-6">
            <label for="provider_name" class="form-label">Provider Name:</label>
            <input type="text" id="provider_name" v-model="order.provider_name" placeholder="Enter Provider Name"
              class="form-control" required />
          </div>
        </div>
      </div>

      <!-- Encounter Date -->
      <div class="date-field mb-3">
        <label for="encounter_date" class="form-label">Encounter Date:</label>
        <input type="date" id="encounter_date" v-model="order.encounter_date" class="form-control" required />
      </div>

      <!-- Order Items -->
      <div class="order-items mb-4">
        <h3 class="mb-3">Order Items</h3>
        <div v-for="(item, index) in order.items" :key="index" class="item-row row align-items-center mb-2">
          <div class="col-md-2">
            <label for="item-name">Item Name:</label>
            <input type="text" v-model="item.name" placeholder="Item Name" class="form-control" id="item-name"
              required />
          </div>

          <div class="col-md-2">
            <label for="unit-price">Unit Price:</label>
            <input type="number" v-model="item.unit_price" @input="calculateSubtotal(item)" placeholder="Unit Price"
              class="form-control" id="unit-price" required />
          </div>

          <div class="col-md-4 d-flex align-items-center">
            <label class="me-2" for="quantity">Quantity:</label>
            <button class="btn btn-outline-secondary mx-1" @click="decreaseQty(item)">-</button>
            <input type="number" v-model="item.quantity" @input="calculateSubtotal(item)"
              class="form-control text-center" style="width: 50px;" id="quantity" required />
            <button class="btn btn-outline-secondary mx-1" @click="increaseQty(item)">+</button>
          </div>

          <div class="col-md-2">
            <label for="subtotal">Subtotal:</label>
            <input type="number" v-model="item.subtotal" readonly class="form-control" id="subtotal" required />
          </div>

          <div class="col-md-1">
            <button class="btn btn-danger" @click="removeItem(index)">Remove</button>
          </div>
        </div>

        <button class="btn btn-primary mt-3" @click="addItem">Add Item</button>
      </div>

      <!-- Total Amount -->
      <div class="total-amount text-end">
        <h3>Total: {{ calculateTotal() }}</h3>
      </div>

      <!-- Submit Button with Loader -->
      <div class="text-center mt-4">
        <button class="btn btn-success btn-lg" @click="submitOrder" :disabled="loading">
          <span v-if="!loading">Submit Order</span>
          <span v-else>
            <div class="spinner-border text-light" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </span>
        </button>
      </div>


      <!-- Submitted Orders Table -->
      <div class="mt-5">
        <h3 class="text-center mb-4">Submitted Order Items</h3>
        <div class="container mt-4">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead class="table-dark">
                <tr>
                  <th>Provider Name</th>
                  <th>Item Name</th>
                  <th>Unit Price</th>
                  <th>Quantity</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <!-- Loop through each provider's orders -->
                <template v-for="(
                                        orders, providerName
                                    ) in groupedOrders" :key="providerName">
                  <!-- Loop through each order for the provider -->
                  <template v-for="(order, orderIndex) in orders" :key="order.id">
                    <!-- Provider Name: Displayed once per provider with rowspan for all items -->
                    <tr v-for="(
                                                item, itemIndex
                                            ) in order.items" :key="item.id">
                      <!-- Show provider name in the first row of each order with rowspan across all items -->
                      <td v-if="itemIndex === 0" :rowspan="order.items.length">
                        {{ providerName }}
                      </td>

                      <!-- Display the item details -->
                      <td>{{ item.item_name }}</td>
                      <td>{{ item.unit_price }}</td>
                      <td>{{ item.quantity }}</td>
                      <td>{{ item.subtotal }}</td>
                    </tr>
                  </template>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading: false,
      order: {
        hmo_code: "",
        provider_name: "",
        encounter_date: "",
        items: [{ name: "", unit_price: 0, quantity: 1, subtotal: 0 }],
      },
      submittedOrders: [], // To store submitted orders
      groupedOrders: {},
      flashMessage: "", // For flash success message
      errorMessage: "", // For error message
    };
  },
  methods: {
    // Method to get the total number of items for a provider
    getTotalItemsForProvider(orders) {
      return orders.reduce(
        (total, order) => total + order.items.length,
        0
      );
    },

    // Fetch orders from the API and group them by provider_name
    fetchOrders() {
      fetch("/api/orders")
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Group orders by provider_name
            this.groupedOrders = data.data.reduce((acc, order) => {
              if (!acc[order.provider_name]) {
                acc[order.provider_name] = [];
              }
              acc[order.provider_name].push(order);
              return acc;
            }, {});

            // Optionally log the grouped orders for debugging
            console.log(this.groupedOrders);
          } else {
            this.errorMessage = "Error: Data not found";
            setTimeout(() => {
              this.errorMessage = "";
            }, 3000);
          }
        })
        .catch((error) => {
          this.errorMessage = "Error fetching orders";
          setTimeout(() => {
            this.errorMessage = "";
          }, 3000);
        });
    },

    addItem() {
      this.order.items.push({
        name: "",
        unit_price: 0,
        quantity: 1,
        subtotal: 0,
      });
    },
    removeItem(index) {
      if (this.order.items.length > 1) {
        this.order.items.splice(index, 1);
      } else {
        alert("At least one item must be present.");
      }
    },
    increaseQty(item) {
      item.quantity++;
      this.calculateSubtotal(item);
    },
    decreaseQty(item) {
      if (item.quantity > 1) {
        item.quantity--;
        this.calculateSubtotal(item);
      }
    },
    calculateSubtotal(item) {
      item.subtotal = item.unit_price * item.quantity;
    },
    calculateTotal() {
      return this.order.items.reduce(
        (total, item) => total + item.subtotal,
        0
      );
    },
    submitOrder() {
      // Validate if all fields are filled
      const allFieldsFilled = this.order.hmo_code && this.order.provider_name && this.order.encounter_date;

      // Check if all items have their necessary fields filled
      const allItemsFilled = this.order.items.every(item =>
        item.name && item.unit_price && item.quantity && item.subtotal
      );

      if (!allFieldsFilled || !allItemsFilled) {
        this.errorMessage = "Please fill all required fields.";
        setTimeout(() => {
          this.errorMessage = "";
        }, 3000);
        return; // Stop form submission if validation fails
      }

      // If validation passes, proceed with form submission
      this.loading = true;
      const payload = {
        ...this.order,
        total_amount: this.calculateTotal(),
      };

      fetch("/api/orders", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            this.submittedOrders = Array.isArray(
              this.submittedOrders
            )
              ? [...this.submittedOrders, data.order]
              : [data.order]; // In case it's not an array, initialize it with the first order

            this.clearForm();
            this.flashMessage =
              "Order has been successfully submitted!";
            // Fetch latest orders from the server to include the newly submitted order
            this.fetchOrders();
            setTimeout(() => {
              this.flashMessage = "";
            }, 3000);
          } else {
            this.errorMessage = "Failed to submit order";
            setTimeout(() => {
              this.errorMessage = "";
            }, 3000);
          }
          this.loading = false;
        })
        .catch((error) => {
          this.errorMessage = "Error submitting order";
          setTimeout(() => {
            this.errorMessage = "";
          }, 3000);
          this.loading = false;
        });
    },
    clearForm() {
      this.order = {
        hmo_code: "",
        provider_name: "",
        encounter_date: "",
        items: [{ name: "", unit_price: 0, quantity: 1, subtotal: 0 }],
      };
    },
  },
  mounted() {
    this.fetchOrders();
  },
};
</script>

<style scoped>
/* Styling for the form, buttons, and messages */
.table td,
.table th {
  vertical-align: middle;
}

.order-form {
  width: 60%;
  background: white;
  padding: 30px;
  border-radius: 10px;
}
</style>
