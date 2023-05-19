export default function Checkbox({ name, checked = false, handleChange }) {
  return (
    <input
      type="checkbox"
      name={name}
      checked={checked}
      className="rounded border-slate-600 text-slate-600 shadow-sm focus:border-slate-600 focus:ring focus:ring-slate-200 focus:ring-opacity-50"
      onChange={(e) => handleChange(e)}
      aria-label="Checkbox"
    />
  );
}
