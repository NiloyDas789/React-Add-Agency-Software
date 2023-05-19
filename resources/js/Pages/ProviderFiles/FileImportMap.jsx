import Select from '@/Components/Global/Select';
import CloseIcon from '@/Components/Icons/CloseIcon';
import { v4 as uuid } from 'uuid';

export default function FileImportMap({ index, fieldMap, data, setData, reportFields }) {
  const onHandleChange = (e) => {
    const newFM = [...data.fieldMaps];
    newFM[index][e.target.name] = e.target.value;
    setData('fieldMaps', newFM);
  };

  const onHandleDelete = () => {
    if (data.fieldMaps.length <= 1) return;

    const newFM = [...data.fieldMaps];
    newFM.splice(index, 1);
    setData('fieldMaps', newFM);
  };

  return (
    <div className="flex gap-4 mt-3 items-center justify-center flex-shrink-0">
      <Select
        name="applicationField"
        value={fieldMap.applicationField}
        handleChange={onHandleChange}
        className="w-full"
      >
        <option value="">Select application field</option>
        <option value="call_date_time">Called datetime</option>
        <option value="call_date">Called date</option>
        <option value="call_time">Called time</option>
        <option value="toll_free_number">TFN</option>
        <option value="lead_sku">LeadSKU</option>
        <option value="terminating_number">Terminating number</option>
        <option value="ani">ANI</option>
        <option value="area_code">Area Code</option>
        <option value="duration">Duration</option>
        <option value="call_start_time">Call Duration Start Time</option>
        <option value="call_end_time">Call Duration End Time</option>
        <option value="disposition">Disposition</option>
        <option value="call_status">Call status</option>
        <option value="revenue">Revenue</option>
        <option value="state">State</option>
        <option value="zip_code">Zip code</option>
        <option value="call_recording">Call recording</option>
      </Select>

      <Select
        name="reportField"
        value={fieldMap.reportField}
        handleChange={onHandleChange}
        className="w-full"
      >
        <option value="">Select Report Field</option>
        {reportFields?.map((fld) => (
          <option key={uuid()} value={fld}>
            {fld}
          </option>
        ))}
      </Select>

      <button
        type="button"
        onClick={onHandleDelete}
        className="h-7 w-7 rounded-full flex-shrink-0 flex items-center justify-center shadow-md border border-slate-100"
      >
        <CloseIcon />
      </button>
    </div>
  );
}
