import { useId } from 'react';

export default function Switch({ name, value, handleChange }) {
  const id = useId();

  return (
    <div className="flex flex-col items-start">
      <label htmlFor={id} className="switch flex items-center cursor-pointer w-fit relative">
        <input
          type="checkbox"
          id={id}
          name={name}
          className="sr-only"
          checked={value == 1 ? 'checked' : ''}
          onChange={handleChange}
        />
        <div className="switch-body pointer-events-auto h-6 w-10 rounded-full p-1 ring-1 ring-inset transition duration-200 ease-in-out bg-slate-900/10 ring-slate-900/5"></div>
        <div className="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm ring-1 ring-slate-700/10 transition duration-200 ease-in-out"></div>
      </label>
    </div>
  );
}
