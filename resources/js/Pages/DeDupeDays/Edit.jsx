import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Edit({ auth, deDupeDay }) {
  const { data, setData, put, processing, errors, reset } = useForm({
    days: deDupeDay?.days || '',
    status: deDupeDay?.status || 0,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('de-dupe-days.update', deDupeDay.id), {
      onSuccess: () => {
        toast.success('De Dupe Days Updated successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <div className="flex gap-2">
          <BackButton />
          <Heading className="mb-6 mt-1">Edit State</Heading>
          <Form
            data={data}
            setData={setData}
            submit={submit}
            errors={errors}
            processing={processing}
            isUpdating={true}
          />
        </div>
      </div>
    </Authenticated>
  )
}
