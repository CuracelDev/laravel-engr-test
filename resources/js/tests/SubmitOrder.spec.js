import {
    mount
} from '@vue/test-utils'
import SubmitOrder from '../Components/SubmitOrderForm.vue'

describe('SubmitOrder Page', () => {
    let wrapper
    const mockHmos = [
        { id: 1, name: 'HMO 1' },
        { id: 2, name: 'HMO 2' },
        { id: 3, name: 'HMO 3' },
    ]

    beforeEach(() => {
        wrapper = mount(SubmitOrder, {
            props: {
                hmos: mockHmos
            },
            data: () => ({
                hmo_code: '',
                provider_name: '',
                encounter_date: '',
                items: [{ name: 'Item 0', unit_price: 400, quantity: 3, sub_total: 1200 }]
            })
        })
    })

    const addItem = async (name, unit_price, quantity) => {
        await wrapper.find('button#add-item').trigger('click')
        const items = wrapper.findAll('.order-item')
        const lastItem = items[items.length - 1]

        const unitPrice = lastItem.find('input[name="unit_price"]')
        const quantityInput = lastItem.find('input[name="quantity"]')
        await lastItem.find('input[name="name"]').setValue(name)
        await lastItem.find('input[name="unit_price"]').setValue(unit_price)
        await lastItem.find('input[name="quantity"]').setValue(quantity)

        unitPrice.trigger('input');
        unitPrice.trigger('keyup');
        quantityInput.trigger('input');
        quantityInput.trigger('keyup');
    }

    describe('Component Rendering', () => {
        it('component renders without errors', () => {
            expect(() => mount(SubmitOrder, {
                props: {
                    hmos: mockHmos
                }})).not.toThrow()
        })

        it('adds an item when add button is clicked', async () => {
            await wrapper.find('button#add-item').trigger('click');

            expect(wrapper.findAll('.order-item').length).toBe(2);
            console.log('Item successfully added through click event');
        });

        it('removes an item when remove button is clicked', async () => {
            // Add three items
            await addItem('Item 1', 10, 2)
            await addItem('Item 2', 20, 1)
            await addItem('Item 3', 30, 3)

            // Check that three items have been added
            expect(wrapper.findAll('.order-item').length).toBe(4)

            // Remove the second item and third item
            const removeButtons = wrapper.findAll('button#remove-item')
            await removeButtons.at(1).trigger('click')

            // Check that one item has been removed and two remain
            expect(wrapper.findAll('.order-item').length).toBe(3)

            // Validate that the correct items remain
            const remainingItems = wrapper.findAll('.order-item')
            expect(remainingItems.at(1).find('input[name="name"]').element.value).toBe('Item 2')
            expect(remainingItems.at(2).find('input[name="name"]').element.value).toBe('Item 3')
        })
    })
    
    describe('Order Price Calculation', () => {
        it('calculates subtotal and total when unit price and quantity are inputted', async () => {
            await addItem('Item 1', 100, 2)
            await addItem('Item 2', 500, 3)

            expect(wrapper.vm.form.items[1].sub_total).toBe(200);
            expect(wrapper.vm.form.items[2].sub_total).toBe(1500);
            expect(parseFloat(wrapper.find('#total').element.value)).toBe(1700);

            // when a new item is added, the total should recalculate
            await addItem('Item 3', 300, 4)

            expect(parseFloat(wrapper.find('#total').element.value)).toBe(2900);

            // when a new item is removed, the total should recalculate
            await addItem('Item 4', 500, 4)

            const removeButtons = wrapper.findAll('button#remove-item')
            await removeButtons.at(3).trigger('click')

            expect(parseFloat(wrapper.find('#total').element.value)).toBe(3700);
        });
    })
})
