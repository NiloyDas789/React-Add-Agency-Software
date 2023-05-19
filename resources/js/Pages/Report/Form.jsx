import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import Select from '@/Components/Global/Select';
import Switch from '@/Components/Global/Switch';

export default function Form({ data, setData, submit, errors, processing, isUpdating = 0 }) {
  const onHandleChange = (event) => {
    setData(
      event.target.name,
      event.target.type === 'checkbox' ? event.target.checked : event.target.value
    );
  };

  return (
    <form onSubmit={submit}>
      <div className="md:grid md:grid-cols-2 md:gap-4 space-y-4 md:space-y-0">
        <div>
          <Label forInput="called_at" value="Called AT" />
          <Input
            type="text"
            name="called_at"
            value={data.called_at}
            className="mt-1 block w-full"
            autoComplete="called_at"
            handleChange={onHandleChange}
            readOnly={true}
          />
          <InputError message={errors.called_at} className="mt-2" />
        </div>

        <div>
          <Label forInput="toll_free_number" value="TFN" required />
          <Input
            type="number"
            name="toll_free_number"
            value={data.toll_free_number}
            className="mt-1 block w-full"
            autoComplete="toll_free_number"
            isFocused={true}
            handleChange={onHandleChange}
            readOnly={true}
            required
          />
          <InputError message={errors.toll_free_number} className="mt-2" />
        </div>

        <div>
          <Label forInput="terminating_number" value="Terminating Number" />
          <Input
            type="number"
            name="terminating_number"
            value={data.terminating_number}
            className="mt-1 block w-full"
            autoComplete="terminating_number"
            handleChange={onHandleChange}
            readOnly={true}
          />
          <InputError message={errors.terminating_number} className="mt-2" />
        </div>

        <div>
          <Label forInput="ani" value="ANI" />
          <Input
            type="number"
            name="ani"
            value={data.ani}
            className="mt-1 block w-full"
            autoComplete="ani"
            handleChange={onHandleChange}
            readOnly={true}
          />
          <InputError message={errors.ani} className="mt-2" />
        </div>

        <div>
          <Label forInput="duration" value="Duration" />
          <Input
            type="number"
            name="duration"
            value={data.duration}
            className="mt-1 block w-full"
            autoComplete="duration"
            handleChange={onHandleChange}
            readOnly={true}
          />
          <InputError message={errors.duration} className="mt-2" />
        </div>

        <div>
          <Label forInput="disposition" value="Disposition" />
          <Input
            type="text"
            name="disposition"
            value={data.disposition}
            className="mt-1 block w-full"
            autoComplete="disposition"
            handleChange={onHandleChange}
            readOnly={true}
          />
          <InputError message={errors.disposition} className="mt-2" />
        </div>

        <div>
          <Label forInput="call_status" value="Call Status" />
          <Select
            name="call_status"
            value={data.call_status}
            className="mt-1 block w-full"
            handleChange={onHandleChange}
          >
            <option value="">Select option</option>
            <option value="Billable">Billable</option>
            <option value="Credit">Credit</option>
            <option value="Duplicate">Duplicate</option>
            <option value="Qualified">Qualified</option>
            <option value="Not Qualified">Not Qualified</option>
            <option value="Restricted">Restricted</option>
          </Select>
          <InputError message={errors.call_status} className="mt-2" />
        </div>

        <div>
          <Label forInput="state" value="State" />
          <Input
            type="text"
            name="state"
            value={data.state}
            className="mt-1 block w-full"
            autoComplete="state"
            handleChange={onHandleChange}
            readOnly={true}
          />
          <InputError message={errors.state} className="mt-2" />
        </div>

        <div>
          <Label forInput="zip_code" value="Zip Code" />
          <Input
            type="text"
            name="zip_code"
            value={data.zip_code}
            className="mt-1 block w-full"
            autoComplete="zip_code"
            handleChange={onHandleChange}
            readOnly={true}
          />
          <InputError message={errors.zip_code} className="mt-2" />
        </div>

        <div>
          <Label forInput="call_recording" value="Call Recording" />
          <Input
            type="text"
            name="call_recording"
            value={data.call_recording}
            className="mt-1 block w-full"
            autoComplete="call_recording"
            handleChange={onHandleChange}
          />
          <InputError message={errors.call_recording} className="mt-2" />
        </div>

        <div>
          <Label forInput="credit" value="Credit" />
          <Select
            name="credit"
            value={data.credit}
            className="mt-1 block w-full"
            handleChange={onHandleChange}
          >
            <option value="">Select option</option>
            <option value="1">Yes</option>
            <option value="0">No</option>
          </Select>
          <InputError message={errors.credit} className="mt-2" />
        </div>

        <div>
          <Label forInput="credit_reason" value="Credit Reason" />
          <Select
            name="credit_reason"
            value={data.credit_reason}
            className="mt-1 block w-full"
            handleChange={onHandleChange}
          >
            <option value="">Select option</option>
            <option value="1">Courtesy Credit</option>
            <option value="2">Prank Caller</option>
            <option value="3">Robot Dialer</option>
            <option value="4">Spam</option>
          </Select>
          <InputError message={errors.credit_reason} className="mt-2" />
        </div>
      </div>
      <div className="flex items-center justify-end mt-4">
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
    </form>
  );
}
