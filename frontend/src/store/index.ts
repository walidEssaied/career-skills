import { configureStore } from '@reduxjs/toolkit';
import authReducer from './slices/authSlice';
import skillsReducer from './slices/skillsSlice';
import goalsReducer from './slices/goalsSlice';
import careerPathsReducer from './slices/careerPathsSlice';
import mlReducer from './slices/mlSlice';
import coursesReducer from './slices/coursesSlice';

export const store = configureStore({
    reducer: {
        auth: authReducer,
        skills: skillsReducer,
        goals: goalsReducer,
        careerPaths: careerPathsReducer,
        ml: mlReducer,
        courses: coursesReducer,
    },
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware({
            serializableCheck: false,
        }),
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;