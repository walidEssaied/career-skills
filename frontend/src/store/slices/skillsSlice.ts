import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import api, { endpoints } from '../../services/api';

export interface Skill {
    id: number;
    name: string;
    category: string;
    description: string;
    proficiency_level: number;
    target_level: number;
    last_practiced_at: string;
    verified: boolean;
    verification_method?: string;
    endorsements_count: number;
}

interface SkillsState {
    items: Skill[];
    recommendedSkills: Skill[];
    loading: boolean;
    error: string | null;
}

const initialState: SkillsState = {
    items: [],
    recommendedSkills: [],
    loading: false,
    error: null,
};

// Async thunks
export const fetchUserSkills = createAsyncThunk(
    'skills/fetchUserSkills',
    async () => {
        const response = await api.get(endpoints.userSkills);
        return response.data;
    }
);

export const updateSkillLevel = createAsyncThunk(
    'skills/updateSkillLevel',
    async ({ skillId, level }: { skillId: number; level: number }) => {
        const response = await api.put(endpoints.updateSkill(skillId), { proficiency_level: level });
        return response.data;
    }
);

export const fetchRecommendedSkills = createAsyncThunk(
    'skills/fetchRecommendedSkills',
    async () => {
        const response = await api.get(endpoints.recommendedSkills);
        return response.data;
    }
);

const skillsSlice = createSlice({
    name: 'skills',
    initialState,
    reducers: {
        resetSkillsError: (state) => {
            state.error = null;
        },
    },
    extraReducers: (builder) => {
        builder
            // Fetch user skills
            .addCase(fetchUserSkills.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchUserSkills.fulfilled, (state, action) => {
                state.loading = false;
                state.items = action.payload;
            })
            .addCase(fetchUserSkills.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to fetch skills';
            })
            // Update skill level
            .addCase(updateSkillLevel.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(updateSkillLevel.fulfilled, (state, action) => {
                state.loading = false;
                const updatedSkill = action.payload;
                const index = state.items.findIndex(skill => skill.id === updatedSkill.id);
                if (index !== -1) {
                    state.items[index] = updatedSkill;
                }
            })
            .addCase(updateSkillLevel.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to update skill';
            })
            // Fetch recommended skills
            .addCase(fetchRecommendedSkills.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchRecommendedSkills.fulfilled, (state, action) => {
                state.loading = false;
                state.recommendedSkills = action.payload;
            })
            .addCase(fetchRecommendedSkills.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'Failed to fetch recommended skills';
            });
    },
});

export const { resetSkillsError } = skillsSlice.actions;
export default skillsSlice.reducer;
