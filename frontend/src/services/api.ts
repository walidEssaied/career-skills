import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor for API calls
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor for API calls
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Handle 401 Unauthorized response
        if (error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            // Redirect to login page
            window.location.href = '/login';
            return Promise.reject(error);
        }

        return Promise.reject(error);
    }
);

export const endpoints = {
    // Auth
    login: '/login',
    register: '/register',
    logout: '/logout',
    
    // User Profile
    profile: '/profile',
    updateProfile: '/profile/update',
    
    // Skills
    skills: '/skills',
    userSkills: '/user/skills',
    updateSkill: (skillId: number) => `/user/skills/${skillId}`,
    
    // Career Goals
    goals: '/goals',
    createGoal: '/goals',
    updateGoal: (goalId: number) => `/goals/${goalId}`,
    deleteGoal: (goalId: number) => `/goals/${goalId}`,
    
    // Courses
    courses: '/courses',
    enrollCourse: (courseId: number) => `/courses/${courseId}/enroll`,
    updateCourseProgress: (courseId: number) => `/courses/${courseId}/progress`,
    
    // Career Paths
    careerPaths: '/career-paths',
    careerPathDetails: (pathId: number) => `/career-paths/${pathId}`,
    
    // Recommendations
    recommendedSkills: '/recommendations/skills',
    recommendedCourses: '/recommendations/courses',
    recommendedPaths: '/recommendations/career-paths',
    
    // Networking
    mentors: '/mentors',
    connectMentor: (mentorId: number) => `/mentors/${mentorId}/connect`,
    messages: '/messages',
    sendMessage: (userId: number) => `/messages/${userId}`,
};

export default api;
