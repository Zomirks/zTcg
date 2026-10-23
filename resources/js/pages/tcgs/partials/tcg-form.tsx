import type { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { FieldGroup, FieldLabel } from '@/components/ui/field';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import type { TcgFormData } from '@/types/models';

interface Props {
    form: ReturnType<typeof useForm<TcgFormData>>;
    logoPath?: string | null;
    submitLabel: string;
    submittingLabel: string;
    onSubmit: (e: React.SubmitEvent<HTMLFormElement>) => void;
}

export default function TcgForm({
    form,
    logoPath,
    submitLabel,
    submittingLabel,
    onSubmit,
}: Props) {
    const { data, setData, processing, errors } = form;

    return (
        <form onSubmit={onSubmit}>
            <FieldGroup>
                <div className="grid grid-cols-2 gap-4">
                    <Field>
                        <FieldLabel htmlFor="name">
                            Nom <span className="text-destructive">*</span>
                        </FieldLabel>
                        <Input
                            id="name"
                            name="name"
                            onChange={(e) => setData('name', e.target.value)}
                            value={data.name}
                            required
                        />
                        {errors.name && (
                            <p className="text-sm text-destructive">
                                {errors.name}
                            </p>
                        )}
                    </Field>
                    <Field>
                        <FieldLabel htmlFor="logo">Logo</FieldLabel>
                        {logoPath && !data.logo && (
                            <img
                                src={`/storage/${logoPath}`}
                                alt="Logo actuel"
                                className="mb-2 h-16 w-16 rounded border border-border object-contain"
                            />
                        )}
                        <Input
                            id="logo"
                            name="logo"
                            type="file"
                            accept="image/*"
                            onChange={(e) =>
                                setData('logo', e.target.files?.[0] ?? null)
                            }
                        />
                        {errors.logo && (
                            <p className="text-sm text-destructive">
                                {errors.logo}
                            </p>
                        )}
                    </Field>
                </div>
                <Field>
                    <FieldLabel htmlFor="description">Description</FieldLabel>
                    <Input
                        id="description"
                        name="description"
                        onChange={(e) => setData('description', e.target.value)}
                        value={data.description ?? ''}
                    />
                    {errors.description && (
                        <p className="text-sm text-destructive">
                            {errors.description}
                        </p>
                    )}
                </Field>
            </FieldGroup>

            <div className="mt-6 flex justify-center">
                <Button size="lg" type="submit" disabled={processing}>
                    {processing ? submittingLabel : submitLabel}
                </Button>
            </div>
        </form>
    );
}
