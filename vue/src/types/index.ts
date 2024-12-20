export interface User {
  id: string;
  username: string; // Added username property
  email: string;
  role: 'admin' | 'teacher' | 'student';
  is_active: number; // Added is_active property
  created_at: Date; // Added created_at property
  updated_at: Date; // Added updated_at property
}

export interface Teacher {
  id: number; // Changed to number based on the response
  user_id: number; // Changed to number based on the response
  nip: string;
  spesialisasi: string;
  telepon: string;
  created_at: Date; // Added created_at property
  updated_at: Date; // Added updated_at property
  user: User; // Nested user object
}

export interface Student {
  id: string;
  name: string;
  grade: string;
  class: string;
  // Add more student properties as needed
}

export interface Class {
  id: string;
  name: string;
  teacher: string;
  schedule: string;
  // Add more class properties as needed
}

export interface ActivityLog {
  id: string;
  type: string;
  description: string;
  timestamp: Date;
  // Add more activity log properties as needed
}