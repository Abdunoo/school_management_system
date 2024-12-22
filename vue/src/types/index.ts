export interface User {
  id: number;
  username: string; // Added username property
  email: string;
  role: 'admin' | 'teacher' | 'student';
  is_active: number; // Added is_active property
  created_at: Date | String; // Added created_at property
  updated_at: Date | String; // Added updated_at property
}

export interface Teacher {
  id?: number; // Changed to number based on the response
  user_id?: number; // Changed to number based on the response
  nip: string;
  subject_id: number;
  telepon: string;
  created_at?: Date | String;
  updated_at?: Date | String;
  user?: User; 
  subject?: Subject
}

export interface Student {
  id: number;
  name: string;
  grade: string;
  class: string;
  // Add more student properties as needed
}

export interface ClassItem {
  id?: number;
  name?: string;
  academic_year: string;
  homeroom_teacher_id?: number;
  is_active: number;
  created_at?: Date | String;
  updated_at?: Date | String;
  homeroom_teacher?: Teacher; 
}

export interface ActivityLog {
  id: number;
  type: string;
  description: string;
  timestamp: Date | String;
  // Add more activity log properties as needed
}

export interface Subject {
  id?: number;
  name?: string;
  // code?: string;
  created_at?: Date | String;
  updated_at?: Date | String;
}