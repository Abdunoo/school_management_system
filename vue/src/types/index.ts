export interface User {
  id?: number | null; 
  username?: string | null; 
  email?: string | null;
  role?: 'admin' | 'teacher' | 'student' | null;
  is_active?: number | null; 
  created_at?: Date | string | null; 
  updated_at?: Date | string | null; 
}

export interface Teacher {
  id?: number | null; 
  user_id?: number | null; 
  nip?: string | null;
  telepon?: string | null;
  gender?: string | null;
  created_at?: string | null;
  updated_at?: string | null;
  user?: User | null; 
  subjects?: Subject[] | null; 
  subject_ids?: number[] | null; 
}

export interface Student {
  id?: number | null; 
  name?: string | null; 
  grade?: string | null; 
  class?: string | null; 
}

export interface ClassItem {
  id?: number | null; 
  name?: string | null; 
  academic_year?: string | null; 
  homeroom_teacher_id?: number | null; 
  is_active?: number | null; 
  created_at?: Date | string | null; 
  updated_at?: Date | string | null; 
  homeroom_teacher?: Teacher | null; 
}

export interface ActivityLog {
  id?: number | null; 
  type?: string | null; 
  description?: string | null; 
  timestamp?: Date | string | null; 
}

export interface Subject {
  id?: number | null; 
  name?: string | null; 
  created_at?: Date | string | null; 
  updated_at?: Date | string | null; 
}

export interface ClassScheduleItem {
  id?: number | null; 
  class_id?: number | null; 
  subject_id?: number | null; 
  teacher_id?: number | null; 
  day?: string | null; 
  lesson_hours?: number | null; 
  duration?: number | null; 
  class?: ClassItem | null; 
  subject?: Subject | null; 
  teacher?: Teacher | null; 
}

export interface ClassSchedule {
  id?: number | null; 
  name?: string | null; 
  academic_year?: string | null; 
  homeroom_teacher?: string | null; 
  total_duration?: number | null; 
  total_subjects?: number | null; 
}

export interface AcademicYearOption {
  label: string; 
  value: string; 
}

export interface FormErrors {
  name?: string; 
  academic_year?: string; 
  homeroom_teacher_id?: string; 
  [key: string]: string | undefined; 
}

export interface ApiResponse<T> {
  data: T[];
  total: number;
}