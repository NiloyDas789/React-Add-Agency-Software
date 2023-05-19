import React, { useEffect } from 'react';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import PlusIcon from '@/Components/Icons/PlusIcon';
import FileImportMap from './FileImportMap';
import { useState } from 'react';
import * as XLSX from 'xlsx';
import { v4 as uuid } from 'uuid';
import Switch from '@/Components/Global/Switch';

export default function FileImport({ data, setData, errors, fetchedFieldData, disabled }) {
  const [reportFields, setReportFields] = useState([]);
  const [switchState, setSwitchState] = useState(true);

  useEffect(() => {
    if (Object.keys(fetchedFieldData).length !== 0) {
      setFileHeadingToState(reportFields);
    }
  }, [switchState, fetchedFieldData]);

  const handleSwitchState = () => {
    setSwitchState(!switchState);
    setFileHeadingToState(reportFields);
  };

  const handleFile = (file) => {
    const reader = new FileReader();
    const rABS = !!reader.readAsBinaryString;
    reader.onload = (e) => {
      const bstr = e.target.result;
      const wb = XLSX.read(bstr, { type: rABS ? 'binary' : 'array' });
      const wsname = wb.SheetNames[0];
      const ws = wb.Sheets[wsname];
      const fields = XLSX.utils.sheet_to_json(ws, { header: 1 });

      setFileHeadingToState(
        fields[0].filter((item, index) => fields[0].indexOf(item) === index) ?? []
      );
    };
    if (rABS) reader.readAsBinaryString(file);
    else reader.readAsArrayBuffer(file);
  };

  const setFileHeadingToState = (fields) => {
    setReportFields(fields);

    const newFM = [];
    fields.map((item) => {
      if (Object.keys(fetchedFieldData).length > 0 && switchState) {
        if (Object.values(fetchedFieldData).includes(item)) {
          newFM.push({
            applicationField: Object.keys(fetchedFieldData).find(
              (key) => fetchedFieldData[key] === item
            ),
            reportField: item,
          });
        }
      } else {
        newFM.push({ applicationField: '', reportField: item });
      }
    });

    if (newFM.length == 0) {
      fields.map((item) => {
        newFM.push({ applicationField: '', reportField: item });
      });
    }

    setData('fieldMaps', newFM);
  };

  const handleFileChange = (e) => {
    if (e.target?.files[0] !== undefined) {
      const { files } = e.target;
      handleFile(files[0]);
      setData('file', files[0]);
      return;
    }

    setReportFields([]);
    setData('fieldMaps', []);
    setData(e.target.name, '');
  };

  const addFieldMap = () => {
    const newFM = [...data.fieldMaps];
    newFM.splice(data.fieldMaps.length, 0, {});
    setData('fieldMaps', newFM);
  };

  return (
    <>
      <div className="mt-4">
        <Label forInput="file" value="File" required />
        <Input
          type="file"
          name="file"
          className="mt-1 block w-full border p-1"
          autoComplete="file"
          onChange={handleFileChange}
          disabled={disabled}
          required
        />
        <InputError message={errors.file} className="mt-2" />
      </div>

      {typeof data.file === 'object' && (
        <>
          {Object.keys(fetchedFieldData).length !== 0 && (
            <div className="flex mt-2 gap-2">
              <div>Use Saved Format</div>
              <Switch name="status" value={switchState} handleChange={handleSwitchState} />
            </div>
          )}

          <div className="flex justify-around mt-6 mb-2 mr-10">
            <b>Application Field</b>
            <b>Report Field</b>
          </div>

          {data.fieldMaps?.map((fieldMap, index) => (
            <FileImportMap
              key={uuid()}
              index={index}
              data={data}
              setData={setData}
              fetchedFieldData={fetchedFieldData}
              fieldMap={fieldMap}
              reportFields={reportFields}
            />
          ))}

          <div className="mt-4 mr-10 flex justify-center">
            <button
              type="button"
              onClick={addFieldMap}
              className="h-7 w-7 rounded-full flex items-center justify-center shadow-md border border-slate-100"
            >
              <PlusIcon />
            </button>
          </div>
        </>
      )}
    </>
  );
}
