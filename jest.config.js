export default {
    moduleFileExtensions: ['js', 'json', 'vue'],
    transform: {
        '^.+\\.vue$': '@vue/vue3-jest',
        '^.+\\.js?$': 'babel-jest',
    },
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/resources/js/$1',
        '^vue$': '@vue/runtime-dom',
    },
    snapshotSerializers: ['jest-serializer-vue'],
    testEnvironment: 'jsdom',
    testEnvironmentOptions: {
        customExportConditions: ["node", "node-addons"],
    },
    testMatch: ['<rootDir>/resources/js/tests/**/*.spec.(js|jsx|ts|tsx)|**/__tests__/*.(js|jsx|ts|tsx)']
};
