import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Provider } from 'react-redux';
import GoalsDashboard from './components/goals/GoalsDashboard';
import CoursesDashboard from './components/courses/CoursesDashboard';
import CareerPathsDashboard from './components/career-paths/CareerPathsDashboard';
import ProtectedRoute from './components/auth/ProtectedRoute';
import Login from './components/auth/Login';
import { store } from './store';
import Navigation from './components/common/Navigation';
import NetworkingDashboard from './components/networking/NetworkingDashboard';
import MLDashboard from './components/ml/MLDashboard';

const App: React.FC = () => {
  return (
    <Provider store={store}>
      <Router>
        <div className="flex flex-col min-h-screen bg-gray-100 w-[100vw]">
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/" element={
              <ProtectedRoute>
                <>
                  <Navigation />
                  <main className="flex-grow container mx-auto px-4 py-8">
                    <GoalsDashboard />
                  </main>
                </>
              </ProtectedRoute>
            } />
            <Route path="/goals" element={
              <ProtectedRoute>
                <>
                  <Navigation />
                  <main className="flex-grow container mx-auto px-4 py-8">
                    <GoalsDashboard />
                  </main>
                </>
              </ProtectedRoute>
            } />
            <Route path="/courses" element={
              <ProtectedRoute>
                <>
                  <Navigation />
                  <main className="flex-grow container mx-auto px-4 py-8">
                    <CoursesDashboard />
                  </main>
                </>
              </ProtectedRoute>
            } />
            <Route path="/career-paths" element={
              <ProtectedRoute>
                <>
                  <Navigation />
                  <main className="flex-grow container mx-auto px-4 py-8">
                    <CareerPathsDashboard />
                  </main>
                </>
              </ProtectedRoute>
            } />
            <Route path="/networking" element={
              <ProtectedRoute>
                <>
                  <Navigation />
                  <main className="flex-grow container mx-auto px-4 py-8">
                    <NetworkingDashboard />
                  </main>
                </>
              </ProtectedRoute>
            } />
            <Route path="/ml-insights" element={
              <ProtectedRoute>
                <>
                  <Navigation />
                  <main className="flex-grow container mx-auto px-4 py-8">
                    <MLDashboard />
                  </main>
                </>
              </ProtectedRoute>
            } />
          </Routes>
        </div>
      </Router>
    </Provider>
  );
};

export default App;
