export default function Select({ name, value, className = '', required, handleChange, children }) {
  return (
    <select
      name={name}
      value={value}
      className={
        `border-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm ` +
        className
      }
      required={required}
      onChange={(e) => handleChange(e)}
    >
      {children}
    </select>
  );
}
