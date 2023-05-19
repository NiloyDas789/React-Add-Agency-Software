import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Edit({ auth, report }) {
  const { data, setData, put, processing, errors, reset } = useForm({
    provider_id: report.provider_id || '',
    toll_free_number: report.toll_free_number || '',
    terminating_number: report.terminating_number || '',
    ani: report.ani || '',
    duration: report.duration || '',
    disposition: report.disposition || '',
    call_status: report.call_status || '',
    state: report.state || '',
    zip_code: report.zip_code || '',
    call_recording: report.call_recording || '',
    credit: report.credit || '',
    credit_reason: report.credit_reason || '',
    called_at: report.called_at || '',
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('reports.update', report.id), {
      onSuccess: () => {
        toast.success('Report Updated successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit Report</Heading>
        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
          isUpdating={true}
        />
      </div>
    </Authenticated>
  );
}
