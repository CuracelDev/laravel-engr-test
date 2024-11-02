import { mount } from "@vue/test-utils";
import SubmitOrder from "./SubmitOrder.vue";
import { test } from "vitest";

const valueSelector = "[data-testid=stepper-value]";
const buttonSelector = "[data-testid=increment]";

test("renders a todo", () => {
    const wrapper = mount(SubmitOrder);

    // const todo = wrapper.get('[data-test="todo"]');

    // expect(todo.text()).toBe("Learn Vue.js 3");
});
