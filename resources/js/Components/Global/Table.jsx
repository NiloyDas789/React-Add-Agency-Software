function Table({ className = '', children }) {
  return (
    <div className="overflow-auto w-full max-w-full">
      <table className={'table-auto w-full text-left ' + className}>{children}</table>
    </div>
  );
}

const Th = ({ className = '', children }) => {
  return (
    <th
      className={
        'px-4 title-font tracking-wider font-medium py-3 whitespace-nowrap text-sm ' + className
      }
    >
      {children}
    </th>
  );
};

const Td = ({ className = '', children }) => {
  return <td className={'border-t border-slate-200 px-4 py-3 ' + className}>{children}</td>;
};

Table.Th = Th;
Table.Td = Td;

export default Table;
