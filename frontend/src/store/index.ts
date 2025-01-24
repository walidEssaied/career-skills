import { configureStore } from '@reduxjs/toolkit';
import authReducer from './slices/authSlice';
import skillsReducer from './slices/skillsSlice';
import goalsReducer from './slices/goalsSlice';

export const store = configureStore({
    reducer: {
        auth: authReducer,
        skills: skillsReducer,
        goals: goalsReducer,
    },
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;