import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import Select from '@/Components/Global/Select';
import { useState } from 'react';
import FileImport from './FileImport';

export default function Form({
  data,
  setData,
  submit,
  saveAsDraft,
  errors,
  processing,
  providers,
  fetchedFieldData,
  setFetchedFieldData,
}) {
  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value);
    if (event.target.name == 'provider_id') {
      axios.get(route('provider_file_fields.index', event.target.value)).then((response) => {
        setFetchedFieldData(response.data);
      });
    }
  };

  const checkAllFieldMapped = () => {
    if (typeof data.file !== 'object' || data.fieldMaps.length === 0) {
      return true;
    }

    const unMappedFields = data.fieldMaps.filter(
      (item) => !item.applicationField || !item.reportField
    );

    if (unMappedFields.length > 0) return true;
    return false;
  };

  return (
    <form onSubmit={submit}>
      <div>
        <Label forInput="provider" value="Provider" required />
        <Select
          name="provider_id"
          value={data.provider_id}
          className="mt-1 block w-full"
          handleChange={onHandleChange}
          required
        >
          <option value="">Select provider</option>
          {providers?.map((provider) => (
            <option value={provider.id} key={provider.id}>
              {provider.name}
            </option>
          ))}
        </Select>
        <InputError message={errors.provider_id} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="received_at" value="Received At" required />
        <Input
          type="date"
          name="received_at"
          value={data.received_at}
          className="mt-1 block w-full"
          autoComplete="received_at"
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.received_at} className="mt-2" />
      </div>

      <FileImport
        data={data}
        fetchedFieldData={fetchedFieldData}
        setData={setData}
        disabled={data.provider_id === '' ? true : false}
        errors={errors}
      />

      <div className="flex items-center justify-end mt-4">
        <Button
          type="button"
          onClick={() => saveAsDraft()}
          className="ml-4"
          processing={processing || checkAllFieldMapped()}
        >
          Save as Draft
        </Button>
        <Button type="submit" className="ml-4" processing={processing || checkAllFieldMapped()}>
          Import
        </Button>
      </div>

      <div className="max-w rounded overflow-hidden mt-4 border border-gray-300">
        <div className="px-6 py-4">
          <div className="font-bold text-sm mb-2">Notes:</div>

          <ul className="list-disc pl-4 ">
            <li>
              <p className="text-gray-700 text-sm">
                Please, try not to import .xls file rather import .xlsx or .csv file format.
              </p>
            </li>
            <li>
              <p className="text-gray-700 text-sm">
                Check that your provider's file contains proper date time format.
              </p>
            </li>
            <li>
              <p className="text-gray-700 text-sm">
                Check that in TFN assignment has the duration or disposition. If your provider sent
                file with duration then you should assign duration in TFN assignment. Same rule
                apply for disposition. Suppose, your provider provides file with duration column but
                in TFN assignment you didn't assign duration for the corresponding duration column
                rather you assigned disposition then, they will not map properly. So, report will
                generate empty column or row.
              </p>
            </li>
            <li>
              <p className="text-gray-700 text-sm">
                If a TFN assignment's end date is empty or current date is between the rang of TFN's
                start date to end date then import file will map otherwise no mapping will happen.
                For example: if you insert an end date that is from previous day, week, month or
                year then the mapping will not happen. The end date must be greater than today.
              </p>
            </li>
            <li>
              <p className="text-gray-700 text-sm">
                Finally, if any row shows empty value then check that the provider's disposition is
                matching with the offer's disposition or provider's disposition is in the offer's
                disposition.
              </p>
            </li>
          </ul>
        </div>
      </div>
    </form>
  );
}
