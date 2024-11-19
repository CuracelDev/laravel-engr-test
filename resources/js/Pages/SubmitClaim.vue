<template>
  <GuestLayout>
    <Head title="Submit Claim" />

    <div class="card-header">Submit A Claim</div>
    <br />

    <div class="card-body col-12 col-md-6 col-lg-4">
      <form @submit.prevent="validateForm">
        <!-- Provider Name -->
        <div>
          <label for="provider_name">Provider Name</label>
          <input
            type="text"
            id="provider_name"
            v-model="form.provider_name"
            :class="[
              'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
              { 'border-red-500': form.errors.provider_name },
            ]"
          />
          <span v-if="form.errors.provider_name" class="text-red-500">
            {{ form.errors.provider_name }}
          </span>
        </div>

        <!-- Insurer Code -->
        <div class="relative" ref="dropdownContainer">
          <!-- Search Input -->
          <label for="insurer_code">Insurer Code</label>
          <input
            type="text"
            id="insurer_code"
            v-model="searchQuery"
            placeholder="Search by code or name"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            @focus="showDropdown = true"
          />

          <!-- Dropdown for Suggestions -->
          <ul
            v-if="showDropdown && filteredInsurers.length && searchQuery"
            class="absolute z-10 bg-white border rounded mt-1 max-h-48 w-full overflow-y-auto shadow-lg"
          >
            <li
              v-for="insurer in filteredInsurers"
              :key="insurer.id"
              @click="selectInsurer(insurer)"
              class="cursor-pointer p-2 hover:bg-gray-100"
            >
              {{ insurer.code }} - {{ insurer.name }}
            </li>
          </ul>

          <!-- Error Message -->
          <span v-if="form.errors.insurer_code" class="text-red-500">
            {{ form.errors.insurer_code }}
          </span>
        </div>

        <!-- Encounter Date -->
        <div>
          <label for="encounter_date">Encounter Date</label>
          <input
            type="date"
            id="encounter_date"
            v-model="form.encounter_date"
            :class="[
              'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
              { 'border-red-500': form.errors.encounter_date },
            ]"
          />
          <span v-if="form.errors.encounter_date" class="text-red-500">
            {{ form.errors.encounter_date }}
          </span>
        </div>

        <!-- Priority Level -->
        <div>
          <label for="priority_level">Priority Level</label>
          <select
            id="priority_level"
            v-model="form.priority_level"
            :class="[
              'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
              { 'border-red-500': form.errors.priority_level },
            ]"
          >
            <option value="" disabled>Select priority level</option>
            <option
              v-for="level in ['1', '2', '3', '4', '5']"
              :key="level"
              :value="level"
            >
              {{ level }}
            </option>
          </select>
          <span v-if="form.errors.priority_level" class="text-red-500">
            {{ form.errors.priority_level }}
          </span>
        </div>
        <!-- Specialty -->
        <div>
          <label for="specialty">Speciality</label>
          <select
            id="specialty"
            v-model="form.specialty"
            :class="[
              'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
              { 'border-red-500': form.errors.specialty },
            ]"
          >
            <option value="" disabled>Select a speciality</option>
            <option
              v-for="speciality in specialities"
              :key="speciality"
              :value="speciality.id"
            >
              {{ speciality.speciality }}
            </option>
          </select>
          <span v-if="form.errors.specialty" class="text-red-500">
            {{ form.errors.specialty }}
          </span>
        </div>

        <br />
        <!-- Items -->
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Item</th>
              <th>Unit Price</th>
              <th>Qty</th>
              <th>Sub Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in form.items" :key="index">
              <td>{{ index + 1 }}</td>

              <!-- Item Name -->
              <td>
                <input
                  type="text"
                  v-model="item.name"
                  :class="[
                    'shadow appearance-none border rounded w-50 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                    { 'border-red-500': form.errors[`items.${index}.name`] },
                  ]"
                />
                <span v-if="form.errors[`items.${index}.name`]">
                  {{ form.errors[`items.${index}.name`] }}
                </span>
              </td>

              <!-- Unit Price -->
              <td>
                <input
                  type="number"
                  v-model.number="item.unit_price"
                  @input="validateFloat(item, 'unit_price')"
                  :class="[
                    'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                    { 'border-red-500': form.errors[`items.${index}.unit_price`] },
                  ]"
                />
                <span v-if="form.errors[`items.${index}.unit_price`]">
                  {{ form.errors[`items.${index}.unit_price`] }}
                </span>
              </td>

              <!-- Quantity -->
              <td>
                <input
                  type="number"
                  v-model.number="item.qty"
                  @input="validateInteger(item, 'qty')"
                  :class="[
                    'shadow appearance-none border rounded w-20 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                    { 'border-red-500': form.errors[`items.${index}.qty`] },
                  ]"
                />
                <span v-if="form.errors[`items.${index}.qty`]">
                  {{ form.errors[`items.${index}.qty`] }}
                </span>
              </td>

              <!-- Sub Total -->
              <td>
                <input
                  type="number"
                  v-model.number="item.sub_total"
                  :readonly="true"
                  :class="[
                    'shadow appearance-none border rounded w-20 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                    { 'border-red-500': form.errors[`items.${index}.sub_total`] },
                  ]"
                />
                <span v-if="form.errors[`items.${index}.sub_total`]">
                  {{ form.errors[`items.${index}.sub_total`] }}
                </span>
              </td>

              <!-- Remove Button -->
              <td>
                <button
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                  @click.prevent="removeItem(index)"
                >
                  -
                </button>
              </td>
            </tr>
            <tr></tr>
            <!-- Add Item Button and Total -->
            <tr>
              <td></td>
              <td colspan="1">
                <button
                  class="bg-green-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded"
                  @click.prevent="addItem"
                >
                  +
                </button>
              </td>
              <td colspan="3">
                <div class="flex items-center space-x-2">
                  <span>Total:</span>
                  <input
                    type="number"
                    v-model="form.total"
                    :readonly="true"
                    :class="[
                      'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                    ]"
                  />
                </div>
              </td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <br /><br />
        <!-- Submit Button -->
        <div class="text-right">
          <button
            type="submit"
            class="bg-green-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded"
            :disabled="form.processing"
          >
            Submit
          </button>
        </div>

        <!-- Success Message -->
        <div v-if="form.successMessage" class="mt-4 text-green-500">
          {{ form.successMessage }}
        </div>
      </form>
    </div>
  </GuestLayout>
</template>

<script setup>
import { reactive, ref, computed, watch, onBeforeUnmount, onMounted } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const page = usePage();

const insurers = computed(() => page.props.insurers);
const specialities = computed(() => page.props.specialities);

let form = useForm({
  provider_name: "",
  insurer_code: "",
  encounter_date: "",
  priority_level: "",
  specialty: "",
  items: [
    {
      name: "",
      unit_price: 0.0,
      qty: 0,
      sub_total: "",
    },
  ],
  total: "",
  successMessage: "",
  errors: {},
});

const searchQuery = ref(""); // User input for search
const filteredInsurers = ref([...insurers.value]); // Filtered list of insurers
const showDropdown = ref(false);
// Filter insurers based on the search query
const filterInsurers = () => {
  const queryChars = searchQuery.toLowerCase().replace(/\s+/g, "").split("");

  filteredInsurers.value = insurers.value.filter((insurer) => {
    // Check if every character in the query is included in either the code or the name
    const codeMatches = queryChars.every((char) =>
      insurer.code.toLowerCase().includes(char)
    );
    const nameMatches = queryChars.every((char) =>
      insurer.name.toLowerCase().includes(char)
    );

    return codeMatches || nameMatches;
  });
};

// Select an insurer and set its code in the form
const selectInsurer = (insurer) => {
  form.insurer_code = insurer.code;
  searchQuery.value = `${insurer.code} - ${insurer.name}`;
  filteredInsurers.value = []; // Clear dropdown after selection
};

// Close the dropdown on clicking outside
const dropdownContainer = ref(null);
const handleClickOutside = (event) => {
  if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
    showDropdown.value = false;
  }
};

// Method to validate floating-point values for unit price
const validateFloat = (item, field) => {
  if (isNaN(item[field]) || item[field] < 0) {
    item[field] = 0; // Reset to 0 if invalid
  }
  calculateSubTotal(item); // Recalculate sub-total
};

// Method to validate integer values for quantity
const validateInteger = (item, field) => {
  if (!Number.isInteger(item[field]) || item[field] < 0) {
    item[field] = 0; // Reset to 0 if invalid
  }
  calculateSubTotal(item); // Recalculate sub-total
};

// Method to calculate sub-total for an item
const calculateSubTotal = (item) => {
  item.sub_total = item.qty * item.unit_price;
  calculateTotal(); // Recalculate the total
};

// Method to calculate the total of all items
const calculateTotal = () => {
  form.total = form.items.reduce((total, item) => total + item.sub_total, 0);
};

const addItem = () => {
  form.items.push({
    name: "",
    unit_price: 0.0,
    qty: 0,
    sub_total: "",
  });
};

const removeItem = (index) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
    calculateTotal();
  }
};

const validateForm = () => {
  const validationErrors = {};

  // Validate top-level fields
  if (!form.provider_name.trim()) {
    validationErrors.provider_name = "Provider name is required.";
  }
  if (!form.insurer_code.trim()) {
    validationErrors.insurer_code = "Insurer code is required.";
  }
  if (!form.encounter_date) {
    validationErrors.encounter_date = "Encounter date is required.";
  }
  if (!form.priority_level) {
    validationErrors.priority_level = "Priority level date is required.";
  }

  // Validate items
  form.items.forEach((item, index) => {
    if (!item.name.trim()) {
      validationErrors[`items.${index}.name`] = "Item name is required.";
    }
    if (!item.unit_price || item.unit_price <= 0) {
      validationErrors[`items.${index}.unit_price`] =
        "Unit price must be greater than 0.";
    }
    if (!item.qty || item.qty <= 0) {
      validationErrors[`items.${index}.qty`] = "Quantity must be greater than 0.";
    }
    if (!item.sub_total || item.sub_total <= 0) {
      validationErrors[`items.${index}.sub_total`] = "Subtotal must be greater than 0.";
    }
  });

  // If there are validation errors, set them and do not submit
  if (Object.keys(validationErrors).length > 0) {
    Object.assign(form.errors, validationErrors);
    return;
  }

  form.post("/api/claims", {
    preserveState: true,
    headers: {
      Accept: "application/json",
    },
    onBefore: (request) => {
      if (request.data.errors) {
        console.log(page.props.errors.value);
        console.log(page.props.errors);
        console.log(request.data.errors.value);
        form.errors = request.data.errors;
        // return false; // Prevent the default redirect behavior
      }
    },
    onSuccess: (response) => {
      form.successMessage = "Claim submitted successfully!";
      form.reset();
    },
  });
};

// Ensure errors are reset if the user modifies the input
watch([searchQuery, () => form.items], () => {
  calculateTotal;
  const queryChars = searchQuery.value.toLowerCase().replace(/\s+/g, "").split("");

  filteredInsurers.value = insurers.value.filter((insurer) => {
    // Check if every character in the query is included in either the code or the name
    const codeMatches = queryChars.every((char) =>
      insurer.code.toLowerCase().includes(char)
    );
    const nameMatches = queryChars.every((char) =>
      insurer.name.toLowerCase().includes(char)
    );

    return codeMatches || nameMatches;
  });
  showDropdown.value = !!filteredInsurers.value.length;
});

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>
<style>
/* Ensure dropdown does not expand too much */
.max-h-48 {
  max-height: 12rem; /* Adjust as needed */
}

/* For smoother dropdown transitions */
.shadow-lg {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
