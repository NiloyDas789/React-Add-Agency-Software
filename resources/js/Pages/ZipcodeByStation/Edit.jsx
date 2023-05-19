import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Edit({ auth, zipcodeByStation }) {
  const { data, setData, put, processing, errors, reset } = useForm({
    state: zipcodeByStation?.state || '',
    area_code: zipcodeByStation?.area_code || '',
    zip_code: zipcodeByStation?.zip_code || '',
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('zipcodeByStations.update', zipcodeByStation.id), {
      onSuccess: () => {
        toast.success('Row Updated successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit ZipCode State</Heading>
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
