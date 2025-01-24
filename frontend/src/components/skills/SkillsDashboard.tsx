import { ArcElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Tooltip } from 'chart.js';
import React, { useEffect } from 'react';
import { Doughnut } from 'react-chartjs-2';
import { useDispatch, useSelector } from 'react-redux';
import { AppDispatch, RootState } from '../../store';
import { fetchRecommendedSkills, fetchUserSkills, Skill, updateSkillLevel } from '../../store/slices/skillsSlice';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement);

const SkillsDashboard: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { items: skills, recommendedSkills, loading, error } = useSelector((state: RootState) => state.skills);

    useEffect(() => {
        dispatch(fetchUserSkills());
        dispatch(fetchRecommendedSkills());
    }, [dispatch]);

    const handleSkillLevelUpdate = (skillId: number, newLevel: number) => {
        dispatch(updateSkillLevel({ skillId, level: newLevel }));
    };

    const getSkillsByCategory = () => {
        return skills.reduce((acc: { [key: string]: Skill[] }, skill) => {
            if (!acc[skill.category]) {
                acc[skill.category] = [];
            }
            acc[skill.category].push(skill);
            return acc;
        }, {});
    };

    const skillsByCategory = getSkillsByCategory();

    const getSkillProgressData = () => {
        return {
            labels: ['Beginner', 'Intermediate', 'Advanced', 'Expert'],
            datasets: [{
                data: [
                    skills.filter(s => s.proficiency_level <= 2).length,
                    skills.filter(s => s.proficiency_level > 2 && s.proficiency_level <= 3).length,
                    skills.filter(s => s.proficiency_level > 3 && s.proficiency_level <= 4).length,
                    skills.filter(s => s.proficiency_level > 4).length,
                ],
                backgroundColor: [
                    '#bae6fd',
                    '#7dd3fc',
                    '#38bdf8',
                    '#0ea5e9',
                ],
            }],
        };
    };

    if (loading) {
        return <div className="flex justify-center items-center h-64">Loading...</div>;
    }

    if (error) {
        return <div className="text-red-500 text-center">{error}</div>;
    }

    return (
        <div className="container mx-auto px-4 py-8 w-[100vw]">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                {/* Skills Overview */}
                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-2xl font-bold mb-4">Skills Overview</h2>
                    <div className="w-full h-64">
                        <Doughnut data={getSkillProgressData()} />
                    </div>
                </div>

                {/* Recommended Skills */}
                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-2xl font-bold mb-4">Recommended Skills</h2>
                    <div className="space-y-4">
                        {recommendedSkills.map((skill) => (
                            <div key={skill.id} className="border-b pb-4">
                                <h3 className="font-semibold">{skill.name}</h3>
                                <p className="text-gray-600 text-sm">{skill.description}</p>
                                <button 
                                    className="mt-2 bg-primary-500 text-white px-4 py-2 rounded-md text-sm hover:bg-primary-600"
                                    onClick={() => handleSkillLevelUpdate(skill.id, 1)}
                                >
                                    Add to My Skills
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Skills by Category */}
            <div className="mt-8">
                <h2 className="text-2xl font-bold mb-6">Skills by Category</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {Object.entries(skillsByCategory).map(([category, categorySkills]) => (
                        <div key={category} className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-xl font-semibold mb-4">{category}</h3>
                            <div className="space-y-4">
                                {categorySkills.map((skill) => (
                                    <div key={skill.id} className="border-b pb-4">
                                        <div className="flex justify-between items-center">
                                            <h4 className="font-medium">{skill.name}</h4>
                                            <span className="text-sm text-gray-500">
                                                Level {skill.proficiency_level}/5
                                            </span>
                                        </div>
                                        <div className="mt-2">
                                            <input
                                                type="range"
                                                min="1"
                                                max="5"
                                                value={skill.proficiency_level}
                                                onChange={(e) => handleSkillLevelUpdate(skill.id, parseInt(e.target.value))}
                                                className="w-full"
                                            />
                                        </div>
                                        {skill.verified && (
                                            <span className="inline-flex items-center mt-1 text-xs text-green-600">
                                                <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                                </svg>
                                                Verified
                                            </span>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default SkillsDashboard;
