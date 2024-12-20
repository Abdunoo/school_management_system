export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Nunito', 'sans-serif'], // Set Nunito as the default sans-serif font
      },
      colors: {
        primary: '#2563eb', 
        secondary: '#4b5563',
        success: '#16a34a', 
        danger: '#DC2626',  
        warning: '#dc2626', 
      },
      spacing: {
        18: '4.5rem', // Custom spacing for better layout options
        22: '5.5rem',
        36: '9rem',
      },
      boxShadow: {
        card: '0 4px 6px rgba(0, 0, 0, 0.1)', // Subtle shadow for cards
        navbar: '0 2px 4px rgba(0, 0, 0, 0.1)', // Subtle shadow for topbar
      },
      borderRadius: {
        '4xl': '2rem', // Larger border-radius for modern designs
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'), // Enhances default form styles
    require('@tailwindcss/typography'), // For better typography
  ],
};
