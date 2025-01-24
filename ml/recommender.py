#!/usr/bin/env python3

import sys
import json
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.preprocessing import StandardScaler
from sklearn.ensemble import RandomForestClassifier
import pandas as pd
import joblib
import os
from flask import Flask, request, jsonify

app = Flask(__name__)

class MLRecommender:
    def __init__(self):
        self.vectorizer = TfidfVectorizer(stop_words='english')
        self.scaler = StandardScaler()
        self.models_path = os.path.join(os.path.dirname(__file__), 'models')
        
        # Create models directory if it doesn't exist
        if not os.path.exists(self.models_path):
            os.makedirs(self.models_path)

    def recommend_courses(self, user_skills):
        try:
            # Load pre-trained vectorizer and course data
            vectorizer_path = os.path.join(self.models_path, 'vectorizer.joblib')
            courses_path = os.path.join(self.models_path, 'courses_vectors.joblib')
            courses_data_path = os.path.join(self.models_path, 'courses_data.joblib')
            
            if not all(os.path.exists(p) for p in [vectorizer_path, courses_path, courses_data_path]):
                return {'error': 'Models not trained yet'}
            
            vectorizer = joblib.load(vectorizer_path)
            courses_vectors = joblib.load(courses_path)
            courses_data = joblib.load(courses_data_path)
            
            # Transform user skills
            user_text = ' '.join(user_skills)
            user_vector = vectorizer.transform([user_text])
            
            # Calculate similarities
            similarities = cosine_similarity(user_vector, courses_vectors)
            
            # Get top 5 recommendations
            top_indices = similarities.argsort()[0][-5:][::-1]
            
            recommendations = []
            for idx, score in zip(top_indices, similarities[0][top_indices]):
                course = courses_data[idx]
                recommendations.append({
                    'course_id': course['id'],
                    'title': course['title'],
                    'description': course['description'],
                    'match_score': float(score),
                    'skills_covered': course['skills']
                })
            
            return {'recommendations': recommendations}
                
        except Exception as e:
            return {'error': str(e)}

    def predict_career_path(self, user_profile):
        try:
            model_path = os.path.join(self.models_path, 'career_predictor.joblib')
            career_data_path = os.path.join(self.models_path, 'career_paths_data.joblib')
            
            if not all(os.path.exists(p) for p in [model_path, career_data_path]):
                return {'error': 'Models not trained yet'}
            
            model = joblib.load(model_path)
            career_paths = joblib.load(career_data_path)
            
            # Convert user profile to feature vector
            user_skills = set(user_profile.get('skills', []))
            user_experience = user_profile.get('experience', 0)
            
            # Create feature vector
            features = []
            for career in career_paths:
                required_skills = set(career['required_skills'])
                skill_match = len(user_skills & required_skills) / len(required_skills)
                experience_match = 1 if user_experience >= career['required_experience'] else 0
                features.extend([skill_match, experience_match])
            
            predictions = model.predict_proba([features])[0]
            
            # Format predictions
            career_predictions = []
            for prob, career in zip(predictions, career_paths):
                career_predictions.append({
                    'career_id': career['id'],
                    'title': career['title'],
                    'probability': float(prob),
                    'matching_skills': list(user_skills & set(career['required_skills'])),
                    'missing_skills': list(set(career['required_skills']) - user_skills)
                })
            
            # Sort by probability
            career_predictions.sort(key=lambda x: x['probability'], reverse=True)
            
            return {'predictions': career_predictions[:5]}
                
        except Exception as e:
            return {'error': str(e)}

    def analyze_skill_gaps(self, current_skills, target_role):
        try:
            career_data_path = os.path.join(self.models_path, 'career_paths_data.joblib')
            
            if not os.path.exists(career_data_path):
                return {'error': 'Career data not available'}
            
            career_paths = joblib.load(career_data_path)
            
            # Find target career path
            target_career = next((c for c in career_paths if c['title'].lower() == target_role.lower()), None)
            if not target_career:
                return {'error': 'Target role not found'}
            
            current_set = set(current_skills)
            target_set = set(target_career['required_skills'])
            
            missing_skills = list(target_set - current_set)
            extra_skills = list(current_set - target_set)
            common_skills = list(current_set & target_set)
            
            # Calculate skill gap metrics
            completion_percentage = (len(common_skills) / len(target_set)) * 100
            
            # Get recommended learning path
            learning_path = []
            for skill in missing_skills:
                # Find courses that teach this skill
                relevant_courses = self._find_courses_for_skill(skill)
                learning_path.append({
                    'skill': skill,
                    'recommended_courses': relevant_courses
                })
            
            return {
                'target_role': target_career['title'],
                'completion_percentage': completion_percentage,
                'missing_skills': missing_skills,
                'mastered_skills': common_skills,
                'additional_skills': extra_skills,
                'learning_path': learning_path
            }
        except Exception as e:
            return {'error': str(e)}
    
    def _find_courses_for_skill(self, skill):
        try:
            courses_data_path = os.path.join(self.models_path, 'courses_data.joblib')
            if not os.path.exists(courses_data_path):
                return []
            
            courses = joblib.load(courses_data_path)
            relevant_courses = []
            
            for course in courses:
                if skill in course['skills']:
                    relevant_courses.append({
                        'course_id': course['id'],
                        'title': course['title']
                    })
            
            return relevant_courses[:3]  # Return top 3 courses
        except Exception:
            return []

@app.route('/recommend', methods=['POST'])
def recommend():
    try:
        data = request.get_json()
        if not data or 'skills' not in data:
            return jsonify({'error': 'Missing skills data'}), 400
        
        recommender = MLRecommender()
        recommendations = recommender.recommend_courses(data['skills'])
        return jsonify({'recommendations': recommendations})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'healthy'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
