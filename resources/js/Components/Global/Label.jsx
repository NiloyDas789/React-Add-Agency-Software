export default function Label({ forInput, value, required, className = '', children }) {
  return (
    <label htmlFor={forInput} className={`block font-medium text-sm text-slate-700 ` + className}>
      {value ? value : children} {required ? <span className="text-red-500">*</span> : ''}
    </label>
  );
}
